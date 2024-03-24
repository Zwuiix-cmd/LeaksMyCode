<?php

namespace Zwuiix\Listener\Entity;

use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use Zwuiix\Entity\CustomEntityItem;
use Zwuiix\Handler\Staff;

class EntityItemPickup implements Listener
{
    public function onTest(EntityItemPickupEvent $event)
    {
        $event->cancel();
    }
}