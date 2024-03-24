<?php

namespace PracticeCore\Zwuiix\entities;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use PracticeCore\Zwuiix\session\Session;

class EnderPearlProjectile extends EnderPearl
{
    protected float $gravity = 0.065;
    protected float $drag = 0.0085;

    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $shootingEntity, $nbt);
        $this->setScale(0.6);
    }

    protected function onHit(ProjectileHitEvent $event): void
    {
        $owner = $this->getOwningEntity();
        $entity = $event->getEntity();
        if (!$owner instanceof Session) {
            $this->close();
            return;
        }

        if (!$owner->isAlive()) {
            $this->close();
            return;
        }

        if ($owner->getWorld()->getId() !== $this->getWorld()->getId()) {
            $this->close();
            return;
        }

        $vector = $event->getRayTraceResult()->getHitVector();
        $owner->getNetworkSession()->sendDataPacket(MoveActorAbsolutePacket::create($owner->getId(), $vector, $owner->getLocation()->getPitch(), $owner->getLocation()->getYaw(), 0, MoveActorAbsolutePacket::FLAG_GROUND), true);
        $owner->broadcastPackets($owner->getViewers(), [MoveActorAbsolutePacket::create($owner->getId(), $vector->add(0, $owner->getEyeHeight(), 0), $owner->getLocation()->getPitch(), $owner->getLocation()->getYaw(), 0, MoveActorAbsolutePacket::FLAG_FORCE_MOVE_LOCAL_ENTITY)]);

        $this->getWorld()->addParticle($origin = $owner->getPosition(), new EndermanTeleportParticle());
        $this->getWorld()->addSound($origin, new EndermanTeleportSound());
        $owner->teleport($vector, mode: MovePlayerPacket::MODE_NORMAL);
        $owner->attack(new EntityDamageEvent($owner, EntityDamageEvent::CAUSE_CUSTOM, 0));
        $this->getWorld()->addSound($vector, new EndermanTeleportSound());
    }

    public function calculateInterceptWithBlock(Block $block, Vector3 $start, Vector3 $end): ?RayTraceResult
    {
        return parent::calculateInterceptWithBlock($block, $start, $end);
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isCollided) {
            $this->close();
        }
        return parent::entityBaseTick($tickDiff);
    }
}