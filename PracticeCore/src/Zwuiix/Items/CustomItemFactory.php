<?php

namespace Zwuiix\Items;

use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\PotionType;
use pocketmine\utils\SingletonTrait;
use Zwuiix\Enchantements\CustomEnchant;
use Zwuiix\Entity\vanilla\Creeper;
use Zwuiix\Handler\DurabilityHandler;

class CustomItemFactory
{
    use SingletonTrait;

    public function __construct(){
        $item=ItemFactory::getInstance();

        $item->register(new CustomSword(new ItemIdentifier(ItemIds::DIAMOND_SWORD, 0), "Diamond Sword", 18), true);

        $enderpearl=new EnderPearlItem(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), "Ender Pearl");
        $item->register($enderpearl, true);
        CreativeInventory::getInstance()->add($enderpearl);

        foreach(PotionType::getAll() as $type){
            $typeId = PotionTypeIdMap::getInstance()->toId($type);
            $item->register(new PotionItem(new ItemIdentifier(ItemIds::POTION, $typeId), $type->getDisplayName() . " Potion", $type), true);
            $item->register(new SplashPotionItem(new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId), $type->getDisplayName() . ' Splash Potion', $type), true);
        }
    }
}