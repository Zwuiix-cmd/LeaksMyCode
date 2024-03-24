<?php

namespace PlutooCore\handlers\crate;

use pocketmine\item\Item;

class CrateItem
{
    public function __construct(
        protected Item $item,
        protected float $chance
    ) {}

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return float
     */
    public function getChance(): float
    {
        return $this->chance;
    }
}
