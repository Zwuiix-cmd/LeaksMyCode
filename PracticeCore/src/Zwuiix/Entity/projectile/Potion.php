<?php

declare(strict_types=1);

namespace Zwuiix\Entity\projectile;

use pocketmine\block\Block;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\sound\PotionSplashSound;
use Zwuiix\Player\User;

class Potion extends SplashPotion
{

    public const MAX_HIT = 1.0515;
    public const MAX_MISS = 0.9215;

    protected $gravity = 0.05;
    protected $drag = 0.01;

    private array $colors = array();
    private bool $hasEffects = true;

    public function __construct(Location $location, protected ?Entity $shootingEntity, PotionType $potionType, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $shootingEntity, $potionType, $nbt);
        $effects = $this->getPotionEffects();
        if (count($effects) === 0) {
            $this->colors = [new Color(0x38, 0x5d, 0xc6)];
            $this->hasEffects = false;
        } else {
            foreach ($effects as $effect) {
                $level = $effect->getEffectLevel();
                for ($j = 0; $j < $level; ++$j) {
                    $this->colors[] = $effect->getColor();
                }
            }
        }

        $this->setScale(0.6);

        if($shootingEntity instanceof User) {
            if($shootingEntity->isAlive() && $shootingEntity->getInAirTicks() > 20) {
                $this->teleport($this->shootingEntity->getPosition());
                $this->hit(null, true);
            }
        }
    }

    protected function onHit(ProjectileHitEvent $event): void
    {
        $owner = $this->getOwningEntity();
        if (!$owner instanceof User) {
            $this->flagForDespawn();
            return;
        }

        $this->hit($event);
    }

    public function calculateInterceptWithBlock(Block $block, Vector3 $start, Vector3 $end): ?RayTraceResult
    {
        return parent::calculateInterceptWithBlock($block, $start, $end);
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isCollided) {
            $this->flagForDespawn();
        }
        return parent::entityBaseTick($tickDiff);
    }

    private function hit(?ProjectileHitEvent $event, bool $force = false)
    {
        $packet = new LevelEventPacket();
        $packet->eventId = LevelEvent::PARTICLE_SPLASH;
        $packet->eventData = Color::mix(...$this->colors)->toARGB();
        $packet->position = $this->getPosition()->asVector3();

        $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $packet);
        $this->getWorld()->addSound($this->getPosition(), new PotionSplashSound());

        $shootingEntity=$this->shootingEntity;
        if($force && $shootingEntity instanceof User) {
            if($shootingEntity->isAlive() && $shootingEntity->getInAirTicks() > 20 && !$shootingEntity->isImmobile()) {
                foreach ($this->getPotionEffects() as $effect) {
                    if (!$effect->getType() instanceof InstantEffect) {
                        $newDuration = (int)round($effect->getDuration() * 0.75 * self::MAX_HIT);
                        if ($newDuration < 20) {
                            continue;
                        }
                        $effect->setDuration($newDuration);
                        $shootingEntity->getEffects()->add($effect);
                    } else $effect->getType()->applyEffect($shootingEntity, $effect, self::MAX_HIT, $this);
                }
                $this->flagForDespawn();
                return;
            }
        }
        if ($this->hasEffects && !$force) {
            if ($event instanceof ProjectileHitEntityEvent) {
                $entityHit = $event->getEntityHit();
                if ($entityHit instanceof User) {
                    $entityHit->heal(new EntityRegainHealthEvent($entityHit, 1.45, EntityRegainHealthEvent::CAUSE_CUSTOM));
                }
            }
            foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expand(2.125, 1.125, 2.125)) as $nearby) {
                if ($nearby instanceof User) {
                    if ($nearby->isAlive() and !$nearby->isImmobile()) {
                        foreach ($this->getPotionEffects() as $effect) {
                            if (!$effect->getType() instanceof InstantEffect) {
                                $newDuration = (int)round($effect->getDuration() * 0.75 * self::MAX_HIT);
                                if ($newDuration < 20) {
                                    continue;
                                }
                                $effect->setDuration($newDuration);
                                $nearby->getEffects()->add($effect);
                            } else {
                                $effect->getType()->applyEffect($nearby, $effect, self::MAX_HIT, $this);
                            }
                        }
                    }
                }
            }
        }
    }
}