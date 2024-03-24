<?php

namespace Zwuiix\AdvancedFreeze\Trait;

use Zwuiix\AdvancedFreeze\Trait\DataTrait;
use Zwuiix\AdvancedFreeze\Listener\DataPacketEvent;
use Zwuiix\AdvancedFreeze\Commands\FreezeCommand;
use Zwuiix\AdvancedFreeze\Listener\EventListener;
use Zwuiix\AdvancedFreeze\Main;
use Zwuiix\AdvancedFreeze\Task\ResourcePackSendDataPacket;

trait LoaderTrait
{
    use DataTrait;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->saveDefaultConfig();

        $this->initData();
        $this->getServer()->getCommandMap()->register("", new FreezeCommand($this, $this->getData()->getNested("commands.freeze.name"), $this->getData()->getNested("commands.freeze.description"), $this->getData()->getNested("commands.freeze.aliases")));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ResourcePackSendDataPacket(),20);
        $this->getServer()->getPluginManager()->registerEvents(new DataPacketEvent(), $this);
    }
}