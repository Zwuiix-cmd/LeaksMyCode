<?php

namespace Zwuiix\AdvancedNexus;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\exception\HookAlreadyRegistered;
use Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\PacketHooker;
use Zwuiix\AdvancedNexus\Trait\LoaderTrait;

class Main extends PluginBase
{
    use SingletonTrait, LoaderTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->init();
    }
}
