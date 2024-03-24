<?php

use muqsit\customsizedinvmenu\CustomSizedInvMenuType;
use MusuiEssentials\handlers\ArmorValuesHandler;
use MusuiEssentials\libs\SenseiTarzan\ExtraEvent\Component\EventLoader;
use MusuiEssentials\utils\ItemUtils;
use MusuiEssentials\utils\PathScanner;
use PlutooCore\block\tile\CrateTile;
use PlutooCore\command\CrateCommand;
use PlutooCore\command\KeyCommand;
use PlutooCore\command\LightningCommand;
use PlutooCore\command\RepairCommand;
use PlutooCore\entities\Balloon;
use PlutooCore\entities\FloatingTextEntity;
use PlutooCore\entities\LuckyBlockEntity;
use PlutooCore\entities\OutpostEntity;
use PlutooCore\generator\IslandGenerator;
use PlutooCore\handlers\crate\Crate;
use PlutooCore\handlers\crate\CrateHandler;
use PlutooCore\handlers\crate\CrateItem;
use PlutooCore\handlers\event\Event;
use PlutooCore\handlers\event\EventHandler;
use PlutooCore\handlers\OptionsHandler;
use PlutooCore\handlers\OverwriteHandler;
use PlutooCore\listener\PacketListener;
use PlutooCore\listener\PlayerListener;
use PlutooCore\task\EventTask;
use PlutooCore\task\KothTask;
use PlutooCore\task\OutpostTask;
use PlutooCore\utils\PlutooUtils;
use pocketmine\block\tile\TileFactory;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\Zombie;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;

class PlutooCore extends PluginBase
{
    use SingletonTrait;

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function onLoad(): void
    {
        self::setInstance($this);

        GeneratorManager::getInstance()->addGenerator(IslandGenerator::class, "island", fn() => null, true);
        Server::getInstance()->getWorldManager()->loadWorld("shop");
        Server::getInstance()->getWorldManager()->loadWorld("minage");

        // OVERWRITE
        OverwriteHandler::getInstance()->load();
        ArmorValuesHandler::getInstance()->load();
        TileFactory::getInstance()->register(CrateTile::class, ["crateTile", "erodia:crateTile"]);

        $scan = PathScanner::scanDirectory(Path::join(Server::getInstance()->getDataPath(), "players"));
        foreach ($scan as $path) {
            $username = str_replace([Path::join(Server::getInstance()->getDataPath(), "players"), "/", ".dat"], [], $path);
            $nbt = Server::getInstance()->getOfflinePlayerData($username);
            if(!$nbt instanceof CompoundTag) {
                continue;
            }

            $dataSave = [];
            $rank = $nbt->getString("rank", "non");

        }
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function onEnable(): void
    {
        date_default_timezone_set('Europe/Paris');

        // LISTENER
        EventLoader::loadEventWithClass($this, PlayerListener::class);
        EventLoader::loadEventWithClass($this, PacketListener::class);

        // ENTITY
        PlutooUtils::updateStaticPacketCache("plutonium:balloon_red");
        PlutooUtils::updateStaticPacketCache("plutoonium:lucky_block");
        EntityFactory::getInstance()->register(Balloon::class, function (World $world, CompoundTag $nbt): Balloon {
            return new Balloon(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, [Balloon::getNetworkTypeId()]);

        EntityFactory::getInstance()->register(FloatingTextEntity::class, function (World $world, CompoundTag $nbt): FloatingTextEntity {
            return new FloatingTextEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, [FloatingTextEntity::getNetworkTypeId() . mt_rand(PHP_INT_MIN, PHP_INT_MAX)]);

        EntityFactory::getInstance()->register(OutpostEntity::class, function (World $world, CompoundTag $nbt): OutpostEntity {
            return new OutpostEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, [OutpostEntity::getNetworkTypeId() . mt_rand(PHP_INT_MIN, PHP_INT_MAX)]);
        EntityFactory::getInstance()->register(LuckyBlockEntity::class, function (World $world, CompoundTag $nbt): LuckyBlockEntity {
            return new LuckyBlockEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, [LuckyBlockEntity::getNetworkTypeId()]);

        $this->getScheduler()->scheduleRepeatingTask(new OutpostTask(), 20);
        (new OutpostEntity(new Location(-201, 80, -199, $this->getServer()->getWorldManager()->getDefaultWorld(), 0, 0)))->spawnToAll();

        // EVENT IG
        new EventTask();
        EventHandler::getInstance()->register(new Event("Koth", ["10:00:00", "15:30:00", "00:00:00", "08:00:00"], fn(Event $event)=>$this->getScheduler()->scheduleRepeatingTask(new KothTask($event), 20)));

        $this->loadCrates();

        // COMMANDS
        $this->getServer()->getCommandMap()->register("lightning", new LightningCommand(MusuiEssentials::getInstance(), "lightning", "Manage les éclaires", ["light"]));
        $this->getServer()->getCommandMap()->register("crate", new CrateCommand(MusuiEssentials::getInstance(), "crate", "Crate"));
        $this->getServer()->getCommandMap()->register("key", new KeyCommand(MusuiEssentials::getInstance(), "key", "key"));
        $this->getServer()->getCommandMap()->register("repair", new RepairCommand(MusuiEssentials::getInstance(), "repair", "Repair"));

        foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
            $entities = $world->getEntities();
            foreach ($entities as $entity) {
                if($entity instanceof Human)continue;
                if($entity instanceof ItemEntity or $entity instanceof ExperienceOrb or $entity instanceof Zombie){
                    $entity->flagForDespawn();
                }
            }
        }
    }

    /**
     * @return void
     */
    public function loadCrates(): void
    {
        $packet = StaticPacketCache::getInstance()->getAvailableActorIdentifiers();
        $tag = $packet->identifiers->getRoot();
        assert($tag instanceof CompoundTag);
        $id_list = $tag->getListTag("idlist");
        assert($id_list !== null);
        $id_list->push(CompoundTag::create()
            ->setString("bid", "")
            ->setByte("hasspawnegg", 0)
            ->setString("id", CustomSizedInvMenuType::ACTOR_NETWORK_ID)
            ->setByte("summonable", 0)
        );

        @mkdir(Path::join($this->getDataFolder(), "crates"));
        $scan = PathScanner::scanDirectoryToConfig(Path::join($this->getDataFolder(), "crates"), ["json"], Config::JSON);
        foreach ($scan as $config) {
            $name = $config->get("name", "unknown");
            $loots = $config->get("loots", []);

            $lootsItem = [];
            foreach ($loots as $loot) {
                $lootsItem[] = new CrateItem(ItemUtils::dataToItem($loot["item"]), $loot["chance"] ?? 0);
            }
            CrateHandler::getInstance()->register(new Crate($name, $lootsItem));
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    protected function onDisable(): void
    {
        OptionsHandler::getInstance()->save();
    }
}