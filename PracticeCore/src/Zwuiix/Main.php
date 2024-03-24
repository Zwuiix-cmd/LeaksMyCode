<?php

namespace Zwuiix;

use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Blocks\tile\CrateTile;
use Zwuiix\CombatLogger\EntityDamageByEntity;
use Zwuiix\CombatLogger\PlayerCommandProcess;
use Zwuiix\Entity\projectile\Pearl;
use Zwuiix\Entity\projectile\Potion;
use Zwuiix\Handler\OptimisationHandler;
use Zwuiix\Handler\subhandler\Area;
use Zwuiix\Items\CustomItemFactory;
use Zwuiix\Libs\CortexPE\Commando\PacketHooker;
use Zwuiix\Libs\muqsit\invmenu\InvMenuHandler;
use Zwuiix\Listener\Block\BlockBreak;
use Zwuiix\Listener\Block\BlockBurn;
use Zwuiix\Listener\Block\BlockPlace;
use Zwuiix\Listener\Block\LeavesDecay;
use Zwuiix\Listener\Entity\EntityDamage;
use Zwuiix\Listener\Entity\EntityExplode;
use Zwuiix\Listener\Entity\EntityItemPickup;
use Zwuiix\Listener\Others\InventoryTransaction;
use Zwuiix\Listener\Packet\DataPacketReceive;
use Zwuiix\Listener\Packet\DataPacketSend;
use Zwuiix\Listener\Player\PlayerBucket;
use Zwuiix\Listener\Player\PlayerChangeSkin;
use Zwuiix\Listener\Player\PlayerChat;
use Zwuiix\Listener\Player\PlayerCreation;
use Zwuiix\Listener\Player\PlayerDeath;
use Zwuiix\Listener\Player\PlayerDropItem;
use Zwuiix\Listener\Player\PlayerExhaust;
use pocketmine\command\Command;
use Zwuiix\Listener\Player\PlayerInteract;
use Zwuiix\Listener\Player\PlayerItemConsume;
use Zwuiix\Listener\Player\PlayerItemHeld;
use Zwuiix\Listener\Player\PlayerItemUse;
use Zwuiix\Listener\Player\PlayerJoin;
use Zwuiix\Listener\Player\PlayerLogin;
use Zwuiix\Listener\Player\PlayerMove;
use Zwuiix\Listener\Player\PlayerPreLogin;
use Zwuiix\Listener\Player\PlayerQuit;
use Zwuiix\Listener\Player\PlayerRespawn;
use Zwuiix\Listener\Protection\ModuleListener;
use Zwuiix\Tasks\CombatLogger;
use Zwuiix\Tasks\EnderPearlTask;
use Zwuiix\Trait\{CrashTrait, sub\WorldTrait};
use Zwuiix\Utils\CommandList;

class Main extends PluginBase
{
    use SingletonTrait;

    public array $pearlPlayer = array();
    public Config $playersdata;

    /**
     * @return void
     */
    protected function onEnable(): void
    {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        new CustomItemFactory();
        new ModuleManager($this);

        EntityFactory::getInstance()->register(Pearl::class, function (World $world, CompoundTag $nbt): Pearl {
            return new Pearl(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityLegacyIds::ENDER_PEARL);
        EntityFactory::getInstance()->register(Potion::class, function (World $world, CompoundTag $nbt): Potion {
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort('PotionId', PotionTypeIds::WATER));
            if ($potionType === null) throw new SavedDataLoadingException();
            return new Potion(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotion', 'minecraft:potion', 'thrownpotion'], EntityLegacyIds::SPLASH_POTION);

        foreach(CommandList::getListUnregisterCommand() as $cmd) {
            $command=$this->getServer()->getCommandMap()->getCommand($cmd);
            if(!$command instanceof Command) continue;
            $this->getServer()->getCommandMap()->unregister($command);
        }
        $this->getServer()->getCommandMap()->registerAll("Commands", CommandList::getListCommand($this));

        $this->getServer()->getNetwork()->setName("§9Practice...");
        $this->getScheduler()->scheduleRepeatingTask(new CombatLogger($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new EnderPearlTask(), 1);

        foreach (Server::getInstance()->getNetwork()->getInterfaces() as $interface) {
            if (!$interface instanceof RakLibInterface) continue;
            $interface->setPacketLimit(999999);
        }

        $eventlist=[
            new EntityItemPickup(),
            new PlayerPreLogin(),
            new BlockBurn(),
            new PlayerItemHeld($this),
            new InventoryTransaction(),
            new Listener\Player\PlayerCommandProcess(),
            new PlayerChangeSkin(),
            new LeavesDecay(),
            new PlayerItemUse(),
            new EntityDamage($this),
            new PlayerBucket(),
            new BlockPlace($this),
            new BlockBreak($this),
            new PlayerChat(),
            new PlayerInteract(),
            new PlayerItemConsume(),
            new PlayerRespawn($this),
            new PlayerJoin($this),
            new PlayerQuit($this),
            new PlayerMove($this),
            new PlayerDeath($this),
            new EntityExplode(),
            new PlayerDropItem(),
            new PlayerExhaust(),
            new PlayerCreation(),
            new DataPacketReceive($this),
            new DataPacketSend(),
            new EntityDamageByEntity(),
            new PlayerCommandProcess(),
            new \Zwuiix\CombatLogger\PlayerDeath(),
            new \Zwuiix\CombatLogger\PlayerQuit(),
            new PlayerLogin($this),
            new ModuleListener(),
        ];
        foreach ($eventlist as $value){
            $this->getServer()->getPluginManager()->registerEvents($value, $this);
        }

        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            $world->setTime(0);
            $world->stopTime();
        }

        $this->playersdata=new Config(Main::getInstance()->getDataFolder()."players.json", Config::JSON);
    }

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onDisable(): void
    {
        $this->playersdata->save();
    }
}