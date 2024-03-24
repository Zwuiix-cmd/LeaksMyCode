<?php

namespace PlutooCore\entities;

use MusuiEssentials\MusuiPlayer;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector2;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class Balloon extends Entity
{
    public static array $entity = [];
    public int $stayTime = 0;
    public int $attackCooldown = 0;
    protected int $moveTime = 0;

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0, 0);
    }

    protected function getInitialDragMultiplier(): float
    {
        return 0;
    }

    protected function getInitialGravity(): float
    {
        return 0;
    }

    public static function getNetworkTypeId(): string
    {
        return "plutonium:balloon_red";
    }

    /**
     * @param Location $location
     * @param CompoundTag|null $nbt
     * @param MusuiPlayer|null $musuiPlayer
     */
    public function __construct(Location $location, ?CompoundTag $nbt = null, protected ?MusuiPlayer $musuiPlayer = null)
    {
        parent::__construct($location, $nbt);
    }

    /**
     * @param CompoundTag $nbt
     * @return void
     */
    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        if(!is_null($this->musuiPlayer)) {
            $this->getNetworkProperties()->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, $this->musuiPlayer->getId(), true);
            $this->getNetworkProperties()->setString(EntityMetadataProperties::INTERACTIVE_TAG, "action.interact.leash");
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::LEASHED, true);
        } else $this->kill();
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $up = parent::entityBaseTick($tickDiff);

        if ($this->moveTime > 0) $this->moveTime -= $tickDiff;
        $this->moveToTarget($this->moveTime);

        return $up;
    }

    /**
     * @param int $tickDiff
     * @return void
     */
    public function moveToTarget(int $tickDiff): void
    {
        $target = $this->musuiPlayer;
        if(!$target instanceof MusuiPlayer) return;
        if(!$target->isConnected()) {
            $this->kill();
            return;
        }
        if($this->isClosed()) return;
        $position = $target->getPosition();
        $distance = $this->getPosition()->distance($position);
        $ydiff = $position->getY() - $this->getPosition()->getY();
        $this->setPosition($this->getPosition()->add(0, $ydiff + 1.3, 0));
        $distVector2 = (new Vector2($this->getPosition()->getX(), $this->getPosition()->getY()))->distance(new Vector2($position->getX(), $position->getZ()));

        if($distance >= 30) {
            $this->teleport($target->getPosition()->add(0, 1.5, 0));
            return;
        }

        if($distVector2 <= 2.1) return;

        $x = $position->getX() - $this->getPosition()->getX();
        $z = $position->getZ() - $this->getPosition()->getZ();

        $diff = abs($x) + abs($z);

        if ($x ** 2 + $z ** 2 < 0.7) {
            $this->motion->x = 0;
            $this->motion->z = 0;
        } elseif ($diff > 0) {
            $this->motion->x = 3 * 0.15 * ($x / $diff);
            $this->motion->z = 3 * 0.15 * ($z / $diff);
        }

        $dx = $this->motion->x * $tickDiff;
        $dy = $this->motion->y * $tickDiff;
        $dz = $this->motion->z * $tickDiff;

        if ($this->stayTime > 0) {
            $this->stayTime -= $tickDiff;
            $this->move(0, $dy, 0);
        } else {
            $this->move($dx, $dy, $dz);
        }

        $this->updateMovement();
    }

    public function getName(): string
    {
        return "Balloon";
    }

    public function attack(EntityDamageEvent $source): void{}
}