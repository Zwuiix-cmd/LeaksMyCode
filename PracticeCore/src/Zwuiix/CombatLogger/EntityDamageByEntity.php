<?php

namespace Zwuiix\CombatLogger;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use Zwuiix\Player\User;
use Zwuiix\Tasks\CombatLogger;

class EntityDamageByEntity implements Listener {

    public function setTime(Player $player) {

        if(isset(CombatLogger::$players[$player->getName()])){
            if((time() - CombatLogger::$players[$player->getName()]) > CombatLogger::SECONDS) $player->sendMessage("§cVous êtes désormais en combat.");
        }else $player->sendMessage("§cVous êtes désormais en combat.");

        CombatLogger::$players[$player->getName()] = time();

    }

    public function onDamage(EntityDamageEvent $event) : void {

        if ($event instanceof EntityDamageByEntityEvent) {

            if ($event->getDamager() instanceof User and $event->getEntity() instanceof User) {

                if ($event->isCancelled()) return;

                foreach ([$event->getDamager(), $event->getEntity()] as $players) {

                    $this->setTime($players);
                }
            }
        }
    }
}