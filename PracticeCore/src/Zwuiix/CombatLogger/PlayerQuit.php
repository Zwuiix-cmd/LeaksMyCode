<?php

namespace Zwuiix\CombatLogger;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use Zwuiix\Tasks\CombatLogger;

class PlayerQuit implements Listener {

    public function PlayerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();

        if($event->getQuitReason()=="client disconnect" or $event->getQuitReason()=="client timeout"){
            if(isset(CombatLogger::$players[$player->getName()])){
                $player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_CUSTOM, 99999999));
                $player->kill();
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $player->getCursorInventory()->clearAll();
            }
        }
    }
}