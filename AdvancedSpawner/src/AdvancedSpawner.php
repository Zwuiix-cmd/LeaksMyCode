<?php

use AdvancedSpawner\Zwuiix\entity\MobEntity;
use AdvancedSpawner\Zwuiix\spawner\Spawner;
use AdvancedSpawner\Zwuiix\spawner\SpawnerHandler;
use AdvancedSpawner\Zwuiix\tile\MobSpawnerTile;
use AdvancedSpawner\Zwuiix\trait\EventTrait;
use pocketmine\block\tile\TileFactory;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemBlock;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class AdvancedSpawner extends PluginBase
{
    use SingletonTrait, EventTrait;

    public function onLoad(): void
    {
        self::setInstance($this);
        $this->reloadConfig();
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function onEnable(): void
    {
        foreach ($this->getConfig()->getAll() as $name => $item) {
            $block = LegacyStringToItemParser::getInstance()->parse($item["block"]);
            if(!$block instanceof ItemBlock) {
                continue;
            }
            $drops = [];
            foreach ($item["drops"] as $drop) {
                $drops[] = LegacyStringToItemParser::getInstance()->parse($drop);
            }
            SpawnerHandler::getInstance()->register(new Spawner($name, $item["entityId"], $block, $item["spawnRadius"], $item["spawnDistance"], $item["spawnSpeed"], $drops));
        }
        EntityFactory::getInstance()->register(MobEntity::class, function(World $world, CompoundTag $nbt) : MobEntity{
            return new MobEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['MobEntity']);
        TileFactory::getInstance()->register(MobSpawnerTile::class, ["MobSpawnerTile"]);
        $this->loadEvents();
    }
}