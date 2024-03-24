<?php

namespace PracticeCore\Zwuiix\item;

use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemIdentifier as IID;
use pocketmine\item\ItemTypeIds as Ids;
use pocketmine\item\Sword as SwordPM;
use pocketmine\item\ToolTier;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\enchantment\FakeEnchantment;
use PracticeCore\Zwuiix\handler\LanguageHandler;

class Sword extends SwordPM
{
    use SingletonTrait;

    public function __construct()
    {
        parent::__construct(new IID(Ids::DIAMOND_SWORD), "Diamond Sword", ToolTier::DIAMOND());
        $this->setUnbreakable(true);
        $this->addEnchantment(FakeEnchantment::getInstance()->getEnchantmentInstance());
        $this->setCustomName(LanguageHandler::getInstance()->translate("practice_item"));
        CreativeInventory::getInstance()->add($this);
    }
}