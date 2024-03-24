<?php

declare(strict_types=1);

namespace Zwuiix\Items;

use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\item\VanillaItems;

class PotionItem extends Potion
{
    public function getResidue() : Item{
        return VanillaItems::AIR();
    }
}