<?php

namespace PlutooCore\entities;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class FloatingTextEntity extends Entity
{
    protected function getInitialDragMultiplier() : float{ return 0; }
    protected function getInitialGravity() : float{ return 0; }
    protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.00000000000001, 0.00000000000001, 0.00000000000001); }
    public static function getNetworkTypeId(): string { return EntityIds::PIG; }

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        $this->setScale(0.0001);
        $this->setNoClientPredictions();
        $this->setNameTagAlwaysVisible();
    }

    /**
     * @param Player $player
     * @param Vector3 $clickPos
     * @return bool
     */
    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if($player->getInventory()->getItemInHand()->equals(VanillaBlocks::BEDROCK()->asItem(), false, false)) $this->setHealth(0);
        return false;
    }
}
