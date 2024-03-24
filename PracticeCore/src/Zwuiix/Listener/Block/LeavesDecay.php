<?php

namespace Zwuiix\Listener\Block;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;

class LeavesDecay implements Listener
{
    public function onBlockPlace(LeavesDecayEvent $event) : void
    {
        $event->cancel();
    }
}