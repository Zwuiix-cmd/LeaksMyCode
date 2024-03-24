<?php

namespace Zwuiix\CombatLogger;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use Zwuiix\Tasks\CombatLogger;

class PlayerDeath implements Listener {

    public function PlayerDeathEvent(PlayerDeathEvent $event) {

        if (isset(CombatLogger::$players[$event->getPlayer()->getName()])) unset(CombatLogger::$players[$event->getPlayer()->getName()]);
    }
}