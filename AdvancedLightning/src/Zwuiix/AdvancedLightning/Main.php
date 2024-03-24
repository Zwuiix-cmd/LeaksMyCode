<?php

namespace Zwuiix\AdvancedLightning;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AdvancedLightning\Trait\LoaderTrait;

class Main extends PluginBase
{
    use SingletonTrait, LoaderTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->init();
    }
}
