<?php

namespace PracticeCore\Zwuiix\kit;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\inventory\ArmorInventory;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\item\Armor;
use PracticeCore\Zwuiix\item\EnderPearl;
use PracticeCore\Zwuiix\item\SplashPotion;
use PracticeCore\Zwuiix\item\Sword;

class NodebuffKit extends Kit
{
    use SingletonTrait;

    public function __construct()
    {
        $inventoryContents = [
            clone Sword::getInstance(),
            clone EnderPearl::getInstance()->setCount(16),
        ];
        for ($i = 2; $i < 36; $i++) $inventoryContents[] = clone SplashPotion::getInstance();

        parent::__construct("Nodebuff", $inventoryContents, [
            Armor::get(ArmorInventory::SLOT_HEAD),
            Armor::get(ArmorInventory::SLOT_CHEST),
            Armor::get(ArmorInventory::SLOT_LEGS),
            Armor::get(ArmorInventory::SLOT_FEET),
        ], [VanillaEffects::SPEED(), VanillaEffects::NIGHT_VISION()]);
    }
}