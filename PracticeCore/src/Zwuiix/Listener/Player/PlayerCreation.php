<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use Zwuiix\Player\User;

class PlayerCreation implements Listener
{
    public function onCreate(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(User::class);
    }
}