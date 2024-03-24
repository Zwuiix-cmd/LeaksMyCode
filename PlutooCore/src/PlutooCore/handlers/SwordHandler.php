<?php

namespace PlutooCore\handlers;

use MusuiEssentials\handlers\ArmorValuesHandler;
use PlutooCore\item\CustomSword;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use ReflectionException;
use Symfony\Component\Filesystem\Path;

class SwordHandler
{
    use SingletonTrait;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        self::setInstance($this);
        $this->config = new Config(Path::join(\MusuiEssentials::getInstance()->getDataFolder(), "swords.json"), Config::JSON);
        foreach ($this->config->getAll() as $item) {
            if(!isset($item["id"])) continue;
            if(!isset($item["attackPoints"])) continue;
            if(!isset($item["maxDurability"])) continue;
            $this->registerItem($item["id"], $item["attackPoints"], $item["maxDurability"]);
        }
    }


    /**
     * @param string $itemID
     * @param int $attackPoints
     * @param int $maxDurability
     * @return void
     * @throws ReflectionException
     */
    public function registerItem(string $itemID, int $attackPoints, int $maxDurability): void
    {
        $item = (StringToItemParser::getInstance()->parse($itemID) ?? LegacyStringToItemParser::getInstance()->parse($itemID));
        if(!$item instanceof Item) {
            throw new \Error("Invalid Item Type");
        }

        $newItem = new CustomSword(new ItemIdentifier($item->getTypeId()), $item->getVanillaName(), $attackPoints, $maxDurability);
        ArmorValuesHandler::getInstance()->overwriteItem($itemID, $newItem,
            fn(array &$property, Item $item) => $property[$newItem->getTypeId()] = fn()=>new SavedItemData("minecraft:{$itemID}"),
            fn(array &$property, Item $item) => $property["minecraft:{$itemID}"] = fn ()=>(clone $newItem),
        );
    }
}