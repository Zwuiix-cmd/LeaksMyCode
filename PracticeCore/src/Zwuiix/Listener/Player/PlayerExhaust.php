<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaust implements Listener
{
    public function onExhaust(PlayerExhaustEvent $event)
    {
        $player=$event->getPlayer();
        $player->getHungerManager()->setFood(18);
        $player->getHungerManager()->setSaturation(20);
    }
}