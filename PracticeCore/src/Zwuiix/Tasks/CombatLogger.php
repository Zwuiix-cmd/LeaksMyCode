<?php

namespace Zwuiix\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zwuiix\Main;
use Zwuiix\Player\User;

class CombatLogger extends Task {

    public static array $players = [];

    public const SECONDS = 15;

    public Main $plugin;
    public function __construct(Main $main) {
        $this->plugin= $main;
    }

    public function onRun() : void {

        foreach(CombatLogger::$players as $player=> $time) {

            if((time() - $time) > CombatLogger::SECONDS){

                $p = Server::getInstance()->getPlayerByPrefix($player);

                if($p instanceof User) $p->sendMessage("§aVous n'êtes plus en combat.");
                unset(CombatLogger::$players[$player]);
            }
        }
    }
}