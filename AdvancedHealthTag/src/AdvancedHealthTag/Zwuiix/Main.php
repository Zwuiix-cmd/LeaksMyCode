<?php

namespace AdvancedHealthTag\Zwuiix;

use AdvancedHealthTag\Zwuiix\handler\HealthTagHandler;
use AdvancedHealthTag\Zwuiix\libs\CortexPE\Commando\PacketHooker;
use AdvancedHealthTag\Zwuiix\libs\Zwuiix\AutoLoader\Loader;
use AdvancedHealthTag\Zwuiix\listener\EventListener;
use Phar;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{
    use SingletonTrait;

    protected Config $config;

    /**
     * @return void
     */
    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->saveResource(Path::join("configuration.yml"));
        $this->config  = new Config(Path::join($this->getDataFolder() . "configuration.yml"), Config::YAML);
    }

    /**
     * @return void
     * @throws libs\CortexPE\Commando\exception\HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        if(extension_loaded("xdebug") and (!function_exists('xdebug_info') || count(xdebug_info('mode')) !== 0)){
            $this->getLogger()->warning("xdebug is enabled, this will cause major performance issues with the discord thread.");
        }

        HealthTagHandler::getInstance()->load($this->getConfig());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}