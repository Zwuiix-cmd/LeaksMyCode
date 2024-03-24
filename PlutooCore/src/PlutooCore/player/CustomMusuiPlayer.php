<?php

namespace PlutooCore\player;

use MusuiEssentials\MusuiPlayer;
use PlutooCore\handlers\crate\Crate;
use pocketmine\item\Item;

class CustomMusuiPlayer extends MusuiPlayer
{
    protected bool $isInCreateCrate = false;
    protected ?Crate $createCrate = null;
    /**
     * @return string
     */
    public function getRealName(): string
    {
        return parent::getName();
    }

    /**
     * @return bool
     */
    public function isInCreateCrate(): bool
    {
        return $this->isInCreateCrate;
    }

    /**
     * @return Crate|null
     */
    public function getCreateCrate(): ?Crate
    {
        return $this->createCrate;
    }

    /**
     * @param Crate $crate
     * @return void
     */
    public function setCrate(Crate $crate): void
    {
        $this->createCrate = $crate;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setCreateCrate(bool $value): void
    {
        $this->isInCreateCrate = $value;
    }

    /**
     * @param Item $item
     * @return int
     */
    public function getAllCount(Item $item): int
    {
        $count = 0;
        $items = $this->getInventory()->all($item);
        foreach ($items as $slot => $item) $count = $count + $item->getCount();
        return $count;
    }
}