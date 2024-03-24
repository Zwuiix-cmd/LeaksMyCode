<?php

namespace Zwuiix\CombatLogger;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use Zwuiix\Player\User;
use Zwuiix\Tasks\CombatLogger;
use Zwuiix\Utils\Permissions;

class PlayerCommandProcess implements Listener {

    public function onProcess(PlayerCommandPreprocessEvent $event) : void {

        $player=$event->getPlayer();
        if(!$player instanceof User)return;
        if(isset(CombatLogger::$players[$event->getPlayer()->getName()])) {

            if($event->getMessage()[0] != "/")return;
            if($player->isOp()) return;
            $event->cancel();
            $event->getPlayer()->sendMessage("§cDésolé, vous êtes en combat, vous ne pouvez donc pas utilisez de commande!");
        }
    }
}