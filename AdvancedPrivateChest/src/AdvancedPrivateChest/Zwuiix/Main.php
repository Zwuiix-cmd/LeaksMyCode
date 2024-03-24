<?php

namespace AdvancedPrivateChest\Zwuiix;

use AdvancedPrivateChest\Zwuiix\libs\CortexPE\Commando\PacketHooker;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenuHandler;
use AdvancedPrivateChest\Zwuiix\libs\Zwuiix\AutoLoader\Loader;
use AdvancedPrivateChest\Zwuiix\utils\DropperInventory;
use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Webmozart\PathUtil\Path;

class Main extends PluginBase
{
    use SingletonTrait;

    protected Config $config;
    protected Config $data;

    /**
     * @return void
     */
    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->saveResource(Path::join("configuration.yml"));
    }

    /**
     * @return void
     * @throws libs\CortexPE\Commando\exception\HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        $this->config  = new Config(Path::join($this->getDataFolder() . "configuration.yml"), Config::YAML);
        $this->data  = new Config(Path::join($this->getDataFolder() . "data.json"), Config::JSON);

        InvMenuHandler::getTypeRegistry()->register("invmenu:dropper", new DropperInventory());

        Loader::getInstance()->loadCommand($this, Path::join(Server::getInstance()->getPluginPath(), "AdvancedPrivateChest", "src", "AdvancedPrivateChest", "Zwuiix", "command"));
        Loader::getInstance()->loadTask(Path::join(Server::getInstance()->getPluginPath(), "AdvancedPrivateChest", "src", "AdvancedPrivateChest", "Zwuiix", "task"));
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function onDisable(): void
    {
        Main::getInstance()->getData()->save();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Config
     */
    public function getData(): Config
    {
        return $this->data;
    }
}