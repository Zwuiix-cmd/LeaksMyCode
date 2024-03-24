<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use Zwuiix\Config\Message;
use Zwuiix\Player\User;

class PlayerDropItem implements Listener
{
    public function onDrop(PlayerDropItemEvent $event)
    {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;
        $event->cancel();
    }
}