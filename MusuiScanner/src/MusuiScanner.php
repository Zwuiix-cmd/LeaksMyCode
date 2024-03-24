<?php

use MusuiScanner\Zwuiix\Plugin;
use pocketmine\plugin\PluginBase;
use virion\CortexPE\Commando\exception\HookAlreadyRegistered;

class MusuiScanner extends PluginBase
{
    protected Plugin $plugin;

    public function onLoad(): void
    {
        $this->plugin = new Plugin($this);
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        $this->plugin->enable();
    }
}