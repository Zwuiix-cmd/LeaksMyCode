<?php

namespace PlutooCore\item;

use PlutooCore\interface\EggUI;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Egg extends Item
{
    /**
     * @return int
     */
    public function getMaxStackSize() : int
    {
        return 16;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        EggUI::send($player);
        return ItemUseResult::SUCCESS();
    }
}