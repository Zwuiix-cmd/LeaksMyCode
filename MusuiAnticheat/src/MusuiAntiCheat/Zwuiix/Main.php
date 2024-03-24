<?php

namespace MusuiAntiCheat\Zwuiix;

use JsonException;
use MusuiAntiCheat\Zwuiix\handler\AliasesHandler;
use MusuiAntiCheat\Zwuiix\handler\BanHandler;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\handler\WebhookHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\PacketHooker;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\AsyncIterator;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\AsyncForeachResult;
use MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\InvMenuHandler;
use MusuiAntiCheat\Zwuiix\libs\Zwuiix\AutoLoader\Loader;
use MusuiAntiCheat\Zwuiix\utils\Data;
use Phar;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{
    use SingletonTrait;

    protected Data $data, $blacklist;
    public AsyncIterator $asyncIterator;

    /**
     * @return void
     * @throws JsonException
     */
    protected function onLoad(): void
    {
        if(Phar::running() !== "") {
            throw new PluginException("Cannot be run from phar.");
        }

        self::setInstance($this);
        @mkdir($this->getDataFolder() . "/data/", recursive: true);
        @mkdir($this->getDataFolder() . "/modules/combat/", recursive: true);
        @mkdir($this->getDataFolder() . "/modules/misc/", recursive: true);
        @mkdir($this->getDataFolder() . "/modules/movement/", recursive: true);
        @mkdir($this->getDataFolder() . "/webhooks/", recursive: true);
        $this->saveResource(Path::join("webhooks", "ban.json"));
        $this->saveResource(Path::join("webhooks", "logs.json"));
        $this->saveResource(Path::join("configuration.yml"));
        $this->saveResource(Path::join("lang.ini"));
        $this->saveResource(Path::join("data/voidSkin.png"));
        $this->data = new Data(Path::join($this->getDataFolder() . "configuration.yml"), Data::YAML);
        $this->blacklist = new Data(Path::join($this->getDataFolder() . "blacklist.json"), Data::JSON);

        new AliasesHandler(new Data(Path::join($this->getDataFolder() . "aliases.json"), Data::JSON));
        new LanguageHandler(new Data(Path::join($this->getDataFolder() . "lang.ini"), Data::INI));
        new BanHandler(new Data(Path::join($this->getDataFolder() . "banned.json"), Data::JSON));
        new WebhookHandler();
    }

    /**
     * @return void
     * @throws libs\CortexPE\Commando\exception\HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        if(!file_exists(Path::join(Server::getInstance()->getPluginPath(), "MusuiAntiCheat"))) {
            $this->getLogger()->error("The plugin folder has the wrong name, please correct it!");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return;
        }

        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        if(extension_loaded("xdebug") and (!function_exists('xdebug_info') || count(xdebug_info('mode')) !== 0)){
            $this->getLogger()->warning("xdebug is enabled, this will cause major performance issues with the thread.");
        }

        if(!file_exists(Path::join(Server::getInstance()->getPluginPath(), "MusuiAntiCheat"))) {
            $this->getLogger()->error("Please update libraries.");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return;
        }

        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        Loader::getInstance()->loadModules(Path::join(__DIR__, "modules", "list"));
        Loader::getInstance()->loadListeners($this, Path::join(__DIR__, "listener"));
        Loader::getInstance()->loadCommand(Path::join(__DIR__, "command", "load"));

        $this->asyncIterator = new AsyncIterator($this->getScheduler());
        foreach (Server::getInstance()->getNetwork()->getInterfaces() as $interface) {
            if (!$interface instanceof RakLibInterface) continue;
            $interface->setPacketLimit(PHP_INT_MAX);
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function onDisable(): void
    {
        AliasesHandler::getInstance()->save();
        BanHandler::getInstance()->save();
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @return Data
     */
    public function getBlacklist(): Data
    {
        return $this->blacklist;
    }
}