<?php

namespace Zwuiix\Listener\Block;

use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\Listener;

class BlockBurn implements Listener
{
    public function onBurn(BlockBurnEvent $event)
    {
        $event->cancel();
    }
}