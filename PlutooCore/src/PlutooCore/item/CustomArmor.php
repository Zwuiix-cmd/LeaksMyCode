<?php

namespace PlutooCore\item;

use PlutooCore\handlers\OverwriteHandler;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

class CustomArmor extends Armor
{

    public function __construct(ItemIdentifier $identifier, string $name, ArmorTypeInfo $info, array $enchantmentTags = [], protected string $stringId = "")
    {
        parent::__construct($identifier, $name, $info, $enchantmentTags);
    }

    /**
     * @param int $amount
     * @return bool
     */
    public function applyDamage(int $amount) : bool{
        $amount -= self::getUnbreakingDamageReductions($this, $amount);
        $baseDurability = OverwriteHandler::getInstance()->getBaseDurability($this->stringId);
        $newDurability = self::getMaxDurability();
        if($this->getNamedTag()->getInt("durability", -1) == -1) $this->getNamedTag()->setInt("durability", $newDurability-1);
        $durability = $this->getNamedTag()->getInt("durability");
        $damage = $newDurability / $baseDurability;
        if($durability <= 0) return parent::applyDamage($baseDurability);
        $this->getNamedTag()->setInt("durability", $durability - $amount);
        $damage = intval(round($durability / $damage - $baseDurability) * -1);
        $this->setDamage($damage);

        $this->setLore(["§r§7Durabilité: " . $this->getMaxDurability() - $this->getDamage() . "/" . $this->getMaxDurability()]);

        return true;
    }

    /**
     * @param Item $item
     * @param int $amount
     * @return int
     */
    protected static function getUnbreakingDamageReductions(Item $item, int $amount) : int {
        if (($unbreakingLevel = $item->getEnchantmentLevel(VanillaEnchantments::UNBREAKING())) > 0) {
            $negated = 0;
            $chance = 1 / ($unbreakingLevel + 1);
            for($i = 0; $i < $amount; ++$i) {
                if(mt_rand(1, 100) > 60 and lcg_value() > $chance){
                    $negated++;
                }
            }
            return $negated;
        }
        return 0;
    }
}