<?php

namespace PracticeCore\Zwuiix;

use JsonException;
use Phar;
use pocketmine\inventory\CreativeInventory;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\HookAlreadyRegistered;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\PacketHooker;
use PracticeCore\Zwuiix\trait\CommandTrait;
use PracticeCore\Zwuiix\trait\EntityTrait;
use PracticeCore\Zwuiix\trait\FFATrait;
use PracticeCore\Zwuiix\trait\GeneratorTrait;
use PracticeCore\Zwuiix\trait\ItemTrait;
use PracticeCore\Zwuiix\trait\KitsTrait;
use PracticeCore\Zwuiix\trait\ListenerTrait;
use PracticeCore\Zwuiix\trait\RanksTrait;
use PracticeCore\Zwuiix\trait\ServersTrait;
use PracticeCore\Zwuiix\trait\TaskTrait;
use Symfony\Component\Filesystem\Path;

class PracticeCore
{
    use SingletonTrait;
    use CommandTrait, GeneratorTrait, ListenerTrait, ServersTrait;

    /**
     * @param PluginBase $plugin
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    public function __construct(
        protected PluginBase $plugin
    ) {
        self::setInstance($this);
        $this->loadGenerator();
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
        CreativeInventory::getInstance()->clear();

        @mkdir($plugin->getDataFolder() . "/servers/", recursive: true);
        $this->getPlugin()->saveResource("messages.ini");
        Server::getInstance()->getNetwork()->setName(LanguageHandler::getInstance()->translate("server_motd"));

        $this->loadListener();
        $this->loadCommand();
        $this->loadServers();
    }

    public function __destruct() {}
}