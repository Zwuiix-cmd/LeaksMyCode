<?php

namespace PracticeCore\Zwuiix\enchantment;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\SingletonTrait;

class FakeEnchantment
{
    use SingletonTrait;

    protected EnchantmentInstance $enchantmentInstance;
    protected Enchantment $enchantment;

    public function __construct()
    {
        $this->enchantment = new Enchantment("FakeEnchantment", Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::NONE, 1);
        $this->enchantmentInstance = new EnchantmentInstance($this->enchantment);
        EnchantmentIdMap::getInstance()->register(100, $this->enchantment);
    }

    /**
     * @return EnchantmentInstance
     */
    public function getEnchantmentInstance(): EnchantmentInstance
    {
        return $this->enchantmentInstance;
    }

    public function getEnchantment(): Enchantment
    {
        return $this->enchantment;
    }
}