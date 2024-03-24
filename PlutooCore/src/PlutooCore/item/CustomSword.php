<?php

namespace PlutooCore\item;

use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;

class CustomSword extends Sword
{
    /**
     * @param ItemIdentifier $identifier
     * @param string $name
     * @param int $attackPoints
     * @param int $maxDurability
     */
    public function __construct(ItemIdentifier $identifier, string $name, protected int $attackPoints, protected int $maxDurability)
    {
        parent::__construct($identifier, $name, ToolTier::NETHERITE, []);
    }

    /**
     * @return int
     */
    public function getAttackPoints(): int
    {
        return $this->attackPoints;
    }

    /**
     * @return int
     */
    public function getMaxDurability(): int
    {
        return $this->maxDurability;
    }

    /**
     * @param int $amount
     * @return bool
     */
    public function applyDamage(int $amount) : bool{
        $amount -= self::getUnbreakingDamageReductions($this, $amount);
        $baseDurability = VanillaItems::STONE_SWORD()->getMaxDurability();
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