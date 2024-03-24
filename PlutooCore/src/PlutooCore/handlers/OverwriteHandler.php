<?php

namespace PlutooCore\handlers;

use Closure;
use MusuiEssentials\handlers\ArmorValuesHandler;
use MusuiEssentials\utils\ReflectionUtils;
use PlutooCore\item\CustomArmor;
use PlutooCore\item\Egg;
use PlutooCore\item\Gapple;
use PlutooCore\item\HealStick;
use PlutooCore\item\InfiniteSnowball;
use PlutooCore\item\RedBalloon;
use pocketmine\data\bedrock\item\ItemDeserializer;
use pocketmine\data\bedrock\item\ItemSerializer;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\ToolTier;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionException;

class OverwriteHandler
{
    use SingletonTrait;

    /**
     * @return void
     * @throws ReflectionException
     */
    public function load(): void
    {
        $this->basicItems();
        $this->armorOverOverwrite(); // x)
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function basicItems(): void
    {
        new SwordHandler();
        $this->overwriteItem(ItemTypeNames::NETHERBRICK, ($snowball = new InfiniteSnowball(new ItemIdentifier(ItemTypeIds::NETHER_BRICK), "Boule de neiges infini")),
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData(ItemTypeNames::NETHERBRICK),
            fn(array &$property, Item $item) => $property[ItemTypeNames::NETHERBRICK] = fn() => (clone $item),
        );
        StringToItemParser::getInstance()->override("nether_brick", fn()=>$snowball);

        $this->overwriteItem(ItemTypeNames::SHEARS, new HealStick(new ItemIdentifier(ItemTypeIds::SHEARS), "HealStick"),
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData(ItemTypeNames::SHEARS),
            fn(array &$property, Item $item) => $property[ItemTypeNames::SHEARS] = fn() => (clone $item),
        );
        $this->overwriteItem(ItemTypeNames::GOLDEN_APPLE, new Gapple(new ItemIdentifier(ItemTypeIds::GOLDEN_APPLE), "Golden Apple"),
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData(ItemTypeNames::GOLDEN_APPLE),
            fn(array &$property, Item $item) => $property[ItemTypeNames::GOLDEN_APPLE] = fn() => (clone $item),
        );
        $this->overwriteItem(ItemTypeNames::FLINT_AND_STEEL, new RedBalloon(new ItemIdentifier(ItemTypeIds::FLINT_AND_STEEL), "Ballon §cRouge"),
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData(ItemTypeNames::FLINT_AND_STEEL),
            fn(array &$property, Item $item) => $property[ItemTypeNames::FLINT_AND_STEEL] = fn() => (clone $item),
        );
        $this->overwriteItem(ItemTypeNames::EGG, new Egg(new ItemIdentifier(ItemTypeIds::EGG), "Oeuf en pluto"),
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData(ItemTypeNames::EGG),
            fn(array &$property, Item $item) => $property[ItemTypeNames::EGG] = fn() => (clone $item),
        );
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function armorOverOverwrite(): void
    {
        $config = ReflectionUtils::getProperty(ArmorValuesHandler::class, ArmorValuesHandler::getInstance(), "config");
        if (!$config instanceof Config) return;
        foreach ($config->getAll() as $item) {
            if (!isset($item["id"])) continue;
            if (!isset($item["defensePoints"])) continue;
            if (!isset($item["maxDurability"])) continue;
            $this->registerArmor($item["id"], $item["defensePoints"], $item["maxDurability"]);
        }
    }


    /**
     * @param string $itemID
     * @param int $defensePoints
     * @param int $maxDurability
     * @return void
     * @throws ReflectionException
     */
    public function registerArmor(string $itemID, int $defensePoints, int $maxDurability): void
    {
        $item = (StringToItemParser::getInstance()->parse($itemID) ?? LegacyStringToItemParser::getInstance()->parse($itemID));
        if (!$item instanceof Armor) {
            throw new \Error("Invalid Item Type");
        }

        $armorInfo = ReflectionUtils::getProperty(Armor::class, $item, "armorInfo");
        if (!$armorInfo instanceof ArmorTypeInfo) {
            throw new \Error("Invalid ArmorTypeInfo");
        }

        ReflectionUtils::setProperty(ArmorTypeInfo::class, $armorInfo, "defensePoints", $defensePoints);
        ReflectionUtils::setProperty(ArmorTypeInfo::class, $armorInfo, "maxDurability", $maxDurability);
        ReflectionUtils::setProperty(Armor::class, $item, "armorInfo", $armorInfo);

        $item = new CustomArmor(new ItemIdentifier($item->getTypeId()), $item->getVanillaName(), $armorInfo, $item->getEnchantmentTags(), $itemID);

        $this->overwriteItem($itemID, $item,
            fn(array &$property, Item $item) => $property[$item->getTypeId()] = fn() => new SavedItemData("minecraft:{$itemID}"),
            fn(array &$property, Item $item) => $property["minecraft:{$itemID}"] = fn() => (clone $item),
        );
    }
    /**
     * @param string $id
     * @param Item $item
     * @param Closure $itemSerializers
     * @param Closure $deserializers
     * @return void
     * @throws ReflectionException
     */
    public function overwriteItem(string $id, Item $item, Closure $itemSerializers, Closure $deserializers): void
    {
        $instance = GlobalItemDataHandlers::getSerializer();
        $property = ReflectionUtils::getProperty(ItemSerializer::class, $instance, "itemSerializers");
        ($itemSerializers)($property, $item);
        ReflectionUtils::setProperty(ItemSerializer::class, $instance, "itemSerializers", $property);

        $instance = GlobalItemDataHandlers::getDeserializer();
        $property = ReflectionUtils::getProperty(ItemDeserializer::class, $instance, "deserializers");
        ($deserializers)($property, $item);
        ReflectionUtils::setProperty(ItemDeserializer::class, $instance, "deserializers", $property);

        StringToItemParser::getInstance()->override($id, fn()=> $item);
        $creativeInventory = CreativeInventory::getInstance();
        $property = ReflectionUtils::getProperty(CreativeInventory::class, $creativeInventory, "creative");
        foreach ($property as $key => $value) {
            if($value instanceof Item && $value->equals($item, false, false)) $property[$key] = $item;
        }
        ReflectionUtils::setProperty(CreativeInventory::class, $creativeInventory, "creative", $property);
    }

    /**
     * @param string $id
     * @return int
     */
    public function getBaseDurability(string $id): int
    {
        return match ($id) {
            "chainmail_boots", "iron_boots" => 196,
            "diamond_boots" => 430,
            "golden_boots" => 92,
            "leather_boots" => 66,
            "netherite_boots" => 482,

            "chainmail_chestplate", "iron_chestplate" => 241,
            "diamond_chestplate" => 529,
            "golden_chestplate" => 113,
            "leather_tunic" => 81,
            "netherite_chestplate" => 593,

            "chainmail_helmet", "iron_helmet" => 166,
            "diamond_helmet" => 364,
            "golden_helmet" => 78,
            "leather_cap" => 56,
            "netherite_helmet" => 408,
            "turtle_helmet" => 276,

            "chainmail_leggings", "iron_leggings" => 226,
            "diamond_leggings" => 496,
            "golden_leggings" => 106,
            "leather_pants" => 76,
            "netherite_leggings" => 556,

            "diamond_sword" => ToolTier::DIAMOND()->getMaxDurability(),
            "golden_sword" => ToolTier::GOLD()->getMaxDurability(),
            "iron_sword" => ToolTier::IRON()->getMaxDurability(),
            "netherite_sword" => ToolTier::NETHERITE()->getMaxDurability(),
            "stone_sword" => ToolTier::STONE()->getMaxDurability(),
            "wooden_sword" => ToolTier::WOOD()->getMaxDurability(),

            default => 0,
        };
    }
}