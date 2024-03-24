<?php

namespace Zwuiix\Listener\Protection;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use Zwuiix\AntiModules\ModuleManager;

class ModuleListener implements Listener
{
    public function onReceive(DataPacketReceiveEvent $event)
    {
        ModuleManager::getInstance()->checkAll($event);
    }
}