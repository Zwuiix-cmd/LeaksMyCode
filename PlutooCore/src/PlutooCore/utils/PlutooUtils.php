<?php

namespace PlutooCore\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\network\mcpe\protocol\AvailableActorIdentifiersPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\TextFormat;
use ReflectionClass;
use ReflectionException;

class PlutooUtils
{
    /**
     * @param string $identifier
     * @param string $behaviourId
     * @return void
     * @throws ReflectionException
     */
    public static function updateStaticPacketCache(string $identifier, string $behaviourId = ""): void {
        $instance = StaticPacketCache::getInstance();
        $staticPacketCache = new ReflectionClass($instance);
        $property = $staticPacketCache->getProperty("availableActorIdentifiers");
        $property->setAccessible(true);
        /** @var AvailableActorIdentifiersPacket $packet */
        $packet = $property->getValue($instance);
        /** @var CompoundTag $root */
        $root = $packet->identifiers->getRoot();
        $idList = $root->getListTag("idlist") ?? new ListTag();
        $idList->push(CompoundTag::create()
            ->setString("id", $identifier)
            ->setString("bid", $behaviourId));
        $packet->identifiers = new CacheableNbt($root);
    }

    public static function dataToItem(array $itemData): Item{
        if(is_int($itemData["id"])){
            $item = (clone LegacyStringToItemParser::getInstance()->parse($itemData["id"].":".($itemData["damage"] ?? 0)))->setCount($itemData["count"] ?? 1);
        }else{
            $item = (clone StringToItemParser::getInstance()->parse($itemData["id"]))->setCount($itemData["count"] ?? 1);
        }
        if(isset($itemData["enchants"])) {
            foreach($itemData["enchants"] as $ename => $level){
                $ench = EnchantmentIdMap::getInstance()->fromId((int)$ename);
                if(is_null($ench)) continue;
                $item->addEnchantment(new EnchantmentInstance($ench, $level));
            }
        }
        if(isset($itemData["durability"])) {
            $item->getNamedTag()->setInt("durability", $itemData["durability"]);
        }
        if(isset($itemData["display_name"])) $item->setCustomName(TextFormat::colorize($itemData["display_name"]));
        if(isset($itemData["damage"]) && $item instanceof Durable) $item->setDamage(intval($itemData["damage"]));
        if(isset($itemData["lore"])) {
            $lore = [];
            foreach($itemData["lore"] as $key => $ilore) $lore[$key] = TextFormat::colorize($ilore);
            $item->setLore($lore);
        }
        return $item;
    }

    /**
     * @param Item $item
     * @return array
     */
    public static function itemToData(Item $item): array{
        $serialized = StringToItemParser::getInstance();
        $itemData["id"] = $serialized->lookupAliases($item)[0];
        $itemData["count"] = $item->getCount();
        if($item->hasCustomName())  $itemData["display_name"] = $item->getCustomName();
        if($item->getLore() !== []) $itemData["lore"] = $item->getLore();
        if($item instanceof Durable) $itemData["damage"] = $item->getDamage();
        if(($durability = $item->getNamedTag()->getInt("durability", -1)) != -1) $itemData["durability"] = $durability;
        if($item->hasEnchantments()) {
            foreach($item->getEnchantments() as $enchantment) {
                $itemData["enchants"][(string)EnchantmentIdMap::getInstance()->toId($enchantment->getType())] = $enchantment->getLevel();
            }
        }
        return $itemData;
    }
}