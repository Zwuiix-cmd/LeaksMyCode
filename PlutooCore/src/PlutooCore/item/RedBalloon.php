<?php

namespace PlutooCore\item;

use pocketmine\item\Item;

class RedBalloon extends Item
{
    /**
     * @return int
     */
    public function getMaxStackSize(): int
    {
        return 1;
    }
}