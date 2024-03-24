<?php

namespace AdvancedSpawner\Zwuiix\entity;

use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MobStacker
{
    CONST STACK = "stack";

    public function __construct(
        protected MobEntity $entity
    ) {}

    public function stack(): void
    {
        if ($this->isStacked()) {
            $this->updateNameTag();
            return;
        }
        $mob = $this->findNearStack();
        if (!$mob instanceof MobEntity) {
            $this->entity->nbt->setInt(self::STACK, 1);
            $mobstack = $this;
        } else {
            $this->entity->flagForDespawn();
            $mobstack = new Mobstacker($mob);
            $count = $mob->nbt->getInt(self::STACK);
            $mob->nbt->setInt(self::STACK, ++$count);
        }
        $mobstack->updateNameTag();
    }

    public function isStacked(): bool
    {
        return $this->entity->nbt->getTag(self::STACK) !== null;
    }

    public function updateNameTag(): void
    {
        $nbt = $this->entity->nbt;
        $this->entity->setNameTagVisible();
        $this->entity->setNameTagAlwaysVisible(false);
        $this->entity->setNameTag("§6x{$nbt->getInt(self::STACK)} §e" . $this->entity->getName());
    }

    public function findNearStack(int $range = 16): ?Living
    {
        $entity = $this->entity;
        if ($entity->isFlaggedForDespawn() or $entity->isClosed()) return null;
        foreach(
            $this->entity->getWorld()->getNearbyEntities(
                new AxisAlignedBB(
                    $this->entity->getPosition()->getFloorX() - $range,
                    $this->entity->getPosition()->getFloorY() - $range,
                    $this->entity->getPosition()->getFloorZ() - $range,
                    $this->entity->getPosition()->getFloorX() + $range,
                    $this->entity->getPosition()->getFloorY() + $range,
                    $this->entity->getPosition()->getFloorZ() + $range
                ), $this->entity
            ) as $nearbyEntity
        ) {
            if($nearbyEntity instanceof MobEntity){
                if ($entity->getPosition()->distance($nearbyEntity->getPosition()) <= $range and $entity->getName() === $nearbyEntity->getName()) {
                    $ae = new Mobstacker($nearbyEntity);
                    if ($ae->isStacked() && $ae->getStackAmount() < 2019) return $nearbyEntity;
                }
            }
        }
        return null;
    }

    /**
     * @param Player|null $player
     * @return bool
     */
    public function removeStack(Player $player = null): bool
    {
        $entity = $this->entity;
        $nbt = $entity->nbt;
        if (!$this->isStacked() or ($c = $this->getStackAmount()) <= 1) {
            return false;
        }
        $nbt->setInt(self::STACK, --$c);
        $event = new EntityDeathEvent($entity, $drops = $entity->getDrops());
        $event->call();
        $this->updateNameTag();

        foreach ($drops as $drop) {
            if(!$player instanceof Player) {
                $entity->getWorld()->dropItem(new Vector3($entity->getPosition()->getX(), $entity->getPosition()->getY() + 1, $entity->getPosition()->getZ()), $drop);
                continue;
            }

            if($player->getInventory()->canAddItem($drop)) {
                $player->getInventory()->addItem($drop);
            }
        }

        $exp = $entity->getXpDropAmount();
        if ($exp > 0 && $player instanceof Player) $player->getXpManager()->addXp($exp);
        return true;
    }

    /**
     * @return int
     */
    public function getStackAmount(): int
    {
        return $this->entity->nbt->getInt(self::STACK);
    }
}