<?php

namespace Zwuiix\Handler;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\StringToTParser;
use ReflectionProperty;

class CustomEnchantHandler
{
    use SingletonTrait;

    public static array $enchants = [];

    public function init(): void
    {

    }

    public static function registerEnchantment(CustomEnchantHandler $enchant): void
    {
        EnchantmentIdMap::getInstance()->register($enchant->getId(), $enchant);
        self::$enchants[$enchant->getId()] = $enchant;
        StringToEnchantmentParser::getInstance()->register($enchant->name, fn() => $enchant);
        if ($enchant->name !== $enchant->getDisplayName()) StringToEnchantmentParser::getInstance()->register($enchant->getDisplayName(), fn() => $enchant);
    }

    public static function unregisterEnchantment(int|CustomEnchantHandler $id): void
    {
        $id = $id instanceof CustomEnchantHandler ? $id->getId() : $id;
        $enchant = self::$enchants[$id];

        $property = new ReflectionProperty(StringToTParser::class, "callbackMap");
        $property->setAccessible(true);
        $value = $property->getValue(StringToEnchantmentParser::getInstance());
        unset($value[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->name)))]);
        if ($enchant->name !== $enchant->getDisplayName()) unset($value[strtolower(str_replace([" ", "minecraft:"], ["_", ""], trim($enchant->getDisplayName())))]);
        $property->setValue(StringToEnchantmentParser::getInstance(), $value);

        unset(self::$enchants[$id]);

        $property = new ReflectionProperty(EnchantmentIdMap::class, "enchToId");
        $property->setAccessible(true);
        $value = $property->getValue(EnchantmentIdMap::getInstance());
        unset($value[spl_object_id(EnchantmentIdMap::getInstance()->fromId($id))]);
        $property->setValue(EnchantmentIdMap::getInstance(), $value);

        $property = new ReflectionProperty(EnchantmentIdMap::class, "idToEnch");
        $property->setAccessible(true);
        $value = $property->getValue(EnchantmentIdMap::getInstance());
        unset($value[$id]);
        $property->setValue(EnchantmentIdMap::getInstance(), $value);
    }

    /**
     * @return CustomEnchantHandler[]
     */
    public static function getEnchantments(): array
    {
        return self::$enchants;
    }

    public static function getEnchantment(int $id): ?CustomEnchantHandler
    {
        return self::$enchants[$id] ?? null;
    }

    public static function getEnchantmentByName(string $name): ?CustomEnchantHandler
    {
        return ($enchant = StringToEnchantmentParser::getInstance()->parse($name)) instanceof CustomEnchantHandler ? $enchant : null;
    }
}