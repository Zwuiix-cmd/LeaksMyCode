<?php

namespace PracticeCore\Zwuiix;

use JsonException;
use Phar;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper as Helper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Zombie;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemTypeIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;
use PracticeCore\Zwuiix\commands\load\DisguiseCommand;
use PracticeCore\Zwuiix\commands\load\KnockbackCommand;
use PracticeCore\Zwuiix\commands\load\PingCommand;
use PracticeCore\Zwuiix\commands\load\RankCommand;
use PracticeCore\Zwuiix\commands\load\RekitCommand;
use PracticeCore\Zwuiix\commands\load\ReplyCommand;
use PracticeCore\Zwuiix\commands\load\SetAttackCooldownCommand;
use PracticeCore\Zwuiix\commands\load\SetHeightLimiterCommand;
use PracticeCore\Zwuiix\commands\load\SetKnockbackCommand;
use PracticeCore\Zwuiix\commands\load\SetRankCommand;
use PracticeCore\Zwuiix\commands\load\SpawnCommand;
use PracticeCore\Zwuiix\commands\load\TellCommand;
use PracticeCore\Zwuiix\commands\load\UndisguiseCommand;
use PracticeCore\Zwuiix\entities\EnderPearlProjectile;
use PracticeCore\Zwuiix\entities\SplashPotionProjectile;
use PracticeCore\Zwuiix\handler\FFAHandler;
use PracticeCore\Zwuiix\ffa\FFA;
use PracticeCore\Zwuiix\generator\VoidGenerator;
use PracticeCore\Zwuiix\handler\KitHandler;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\RankHandler;
use PracticeCore\Zwuiix\item\Armor;
use PracticeCore\Zwuiix\item\EnderPearl;
use PracticeCore\Zwuiix\item\FreeForAll;
use PracticeCore\Zwuiix\item\Settings;
use PracticeCore\Zwuiix\item\SplashPotion;
use PracticeCore\Zwuiix\item\Sword;
use PracticeCore\Zwuiix\kit\Kit;
use PracticeCore\Zwuiix\kit\NodebuffKit;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\HookAlreadyRegistered;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\PacketHooker;
use PracticeCore\Zwuiix\libs\muqsit\invmenu\InvMenuHandler;
use PracticeCore\Zwuiix\listener\ServerListener;
use PracticeCore\Zwuiix\listener\SessionListener;
use PracticeCore\Zwuiix\rank\Rank;
use PracticeCore\Zwuiix\task\ChatTask;
use PracticeCore\Zwuiix\task\CombatLoggerTask;
use PracticeCore\Zwuiix\task\EnderPearlTask;
use PracticeCore\Zwuiix\task\GuiUpdateTask;
use PracticeCore\Zwuiix\task\ScoreboardTask;
use PracticeCore\Zwuiix\utils\PathScanner;
use Symfony\Component\Filesystem\Path;

class PracticeCore
{
    use SingletonTrait;

    public const SERVER_IP = "colria.fr";
    public const SERVER_PORT = 19132;

    /**
     * @param PluginBase $plugin
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    public function __construct(
        protected PluginBase $plugin
    ) {
        self::setInstance($this);
        $this->enable();
    }

    /**
     * @throws JsonException
     */
    public function __destruct()
    {
        KnockbackHandler::getInstance()->save();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(!$onlinePlayer->isConnected()) continue;
            $onlinePlayer->transfer(self::SERVER_IP, self::SERVER_PORT);
        }
    }

    /**
     * @return PluginBase
     */
    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    public function enable(): void
    {
        $plugin = $this->getPlugin();
        if(!$plugin instanceof \Loader) {
            $plugin->onEnableStateChange(false);
            return;
        }

        if(Phar::running() !== "") {
            $plugin->onEnableStateChange(false);
            return;
        }

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        if(extension_loaded("xdebug") and (!function_exists('xdebug_info') || count(xdebug_info('mode')) !== 0)){
            $this->getPlugin()->getLogger()->warning("xdebug is enabled, this will cause major performance issues with the thread.");
            $plugin->onEnableStateChange(false);
            return;
        }

        if(!PacketHooker::isRegistered()) PacketHooker::register($plugin);
        if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($plugin);
        CreativeInventory::getInstance()->clear();

        @mkdir(Path::join($plugin->getDataFolder() . "/rank/"));
        @mkdir(Path::join($plugin->getDataFolder() . "/ffa/"));
        $this->getPlugin()->saveResource("knockback.yml");
        $this->getPlugin()->saveResource("messages.ini");
        $this->getPlugin()->saveResource("scoreboard.ini");

        new EnderPearlTask();
        new CombatLoggerTask();
        new ScoreboardTask();
        new ChatTask();
        // TODO: SOON new InfiniteEffectsTask();

        new Settings();
        new FreeForAll();
        new Sword();
        new EnderPearl();
        new SplashPotion();
        new Armor(ItemTypeIds::DIAMOND_HELMET, "Diamond Helmet", 3, ArmorInventory::SLOT_HEAD);
        new Armor(ItemTypeIds::DIAMOND_CHESTPLATE, "Diamond Chestplate", 8, ArmorInventory::SLOT_CHEST);
        new Armor(ItemTypeIds::DIAMOND_LEGGINGS, "Diamond Leggings", 7, ArmorInventory::SLOT_LEGS);
        new Armor(ItemTypeIds::DIAMOND_BOOTS, "Diamond Boots", 3, ArmorInventory::SLOT_FEET);

        Server::getInstance()->getPluginManager()->registerEvents(new SessionListener(), $plugin);
        Server::getInstance()->getPluginManager()->registerEvents(new ServerListener(), $plugin);

        $generators = ["void" => VoidGenerator::class];
        foreach($generators as $name => $class) GeneratorManager::getInstance()->addGenerator($class, $name, fn() => null, true);

        EntityFactory::getInstance()->register(SplashPotionProjectile::class, function(World $world, CompoundTag $nbt) : SplashPotionProjectile{
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort(\pocketmine\entity\projectile\SplashPotion::TAG_POTION_ID, PotionTypeIds::WATER));
            if($potionType === null){
                throw new SavedDataLoadingException("No such potion type");
            }
            return new SplashPotionProjectile(Helper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotionSplash']);
        EntityFactory::getInstance()->register(EnderPearlProjectile::class, function(World $world, CompoundTag $nbt) : EnderPearlProjectile{
            return new EnderPearlProjectile(Helper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearlProjectile']);

        $commands = [
            new RankCommand(),
            new SetRankCommand(),
            new PingCommand(),
            new KnockbackCommand(),
            new SetKnockbackCommand(),
            new SetHeightLimiterCommand(),
            new SetAttackCooldownCommand(),
            new SpawnCommand(),
            new RekitCommand(),
            new TellCommand(),
            new ReplyCommand(),
            new DisguiseCommand(),
            new UndisguiseCommand(),
        ];

        /*** @type BaseCommand $command **/
        foreach ($commands as $command) {
            $check = Server::getInstance()->getCommandMap()->getCommand($command->getName());
            if(!is_null($check)) Server::getInstance()->getCommandMap()->unregister($check);
        }
        Server::getInstance()->getCommandMap()->registerAll("practicecore", $commands);

        KitHandler::getInstance()->register(new NodebuffKit());

        $ranks = PathScanner::scanDirectoryToData(Path::join($plugin->getDataFolder() . "/rank/"), ["yml"]);
        foreach ($ranks as $rank) {
            RankHandler::getInstance()->register(new Rank(
                $rank->get("name", "unknown"),
                $rank->get("permissions", []),
                $rank->get("nameTagFormat", "§7{NAME}"),
                $rank->get("chatFormat", "§7{NAME}: {MESSAGE}"),
                $rank->get("default", false),
            ));
        }

        $ffas = PathScanner::scanDirectoryToData(Path::join($plugin->getDataFolder() . "/ffa/"), ["yml"]);
        foreach ($ffas as $ffa) {
            $worldName = $ffa->get("world", "unknown");
            $v = Server::getInstance()->getWorldManager()->loadWorld($worldName, true);
            if(!$v) continue;
            $world = Server::getInstance()->getWorldManager()->getWorldByName($ffa->get("world", "world"));
            if(!$world instanceof World) continue;

            $kitName = $ffa->get("kit", "unknown");
            $kit = KitHandler::getInstance()->getKitByName($kitName);
            if(!$kit instanceof Kit) continue;

            FFAHandler::getInstance()->register(new FFA(
                $ffa->get("name", "unknown"),
                $ffa->get("itemTexture", "textures/items/diamond"),
                $kit,
                $world,
                $ffa->get("build", false),
                $ffa->get("antiInterrupt", false),
            ));
        }
    }
}