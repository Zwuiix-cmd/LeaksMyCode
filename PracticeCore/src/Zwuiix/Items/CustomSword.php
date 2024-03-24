<?php

namespace Zwuiix\Items;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;

class CustomSword extends Sword
{
    public function __construct(ItemIdentifier $identifier, string $name, protected int $maxDamage)
    {
        parent::__construct($identifier, $name, ToolTier::DIAMOND());
    }

    public function getAttackPoints() : int
    {
        return $this->maxDamage;
    }
}