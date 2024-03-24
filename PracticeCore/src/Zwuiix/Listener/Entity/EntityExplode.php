<?php

namespace Zwuiix\Listener\Entity;

use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use Zwuiix\Handler\Protection;

class EntityExplode implements Listener
{
    public function onExplode(EntityExplodeEvent $event)
    {
        $event->cancel();
    }
}