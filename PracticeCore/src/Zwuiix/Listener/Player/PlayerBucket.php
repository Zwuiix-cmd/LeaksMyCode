<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEvent;
use Zwuiix\Handler\Protection;

class PlayerBucket implements Listener
{

    public function onBucket(PlayerBucketEvent $event) : void
    {
        $event->cancel();
    }
}