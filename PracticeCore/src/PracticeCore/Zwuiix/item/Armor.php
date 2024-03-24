<?php

namespace PracticeCore\Zwuiix\item;

use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Armor as ArmorPM;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier as IID;
use PracticeCore\Zwuiix\enchantment\FakeEnchantment;
use PracticeCore\Zwuiix\handler\LanguageHandler;

class Armor extends ArmorPM
{
    /*** @var Armor[] */
    protected static array $armor = array();

    /**
     * @param int $slot
     * @return Armor|null
     */
    public static function get(int $slot): ?Armor
    {
        return self::$armor[$slot] ??  null;
    }

    public function __construct(int $id, string $name, int $defensePoints, int $slot, int $toughness = 2)
    {
        parent::__construct(new IID($id), $name, new ArmorTypeInfo($defensePoints, PHP_INT_MAX, $slot, $toughness));
        $this->setUnbreakable(true);
        $this->addEnchantment(FakeEnchantment::getInstance()->getEnchantmentInstance());
        $this->setCustomName(LanguageHandler::getInstance()->translate("practice_item"));
        CreativeInventory::getInstance()->add($this);
        self::$armor[$slot] = $this;
    }
}