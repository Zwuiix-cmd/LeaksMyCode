<?php

namespace Zwuiix\AdvancedFreeze;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AdvancedFreeze\Compement\ResourcePackManager;
use Zwuiix\AdvancedFreeze\Lib\CortexPE\Commando\PacketHooker;
use Zwuiix\AdvancedFreeze\Trait\LoaderTrait;

class Main extends PluginBase
{
    use SingletonTrait, LoaderTrait;

    private ResourcePackManager $resourcePackManager;

    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->saveResource("resource_packs.yml");
        $this->saveResource("AdvancedFreezeTEXT.zip");
        $this->saveResource("AdvancedFreezeUI.zip");

        $this->resourcePackManager = new ResourcePackManager($this, $this->getDataFolder(), $this->getLogger());
    }

    protected function onEnable(): void
    {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->init();
    }

    /**
     * @return ResourcePackManager
     */
    public function getResourcePackManager(): ResourcePackManager
    {
        return $this->resourcePackManager;
    }
}
