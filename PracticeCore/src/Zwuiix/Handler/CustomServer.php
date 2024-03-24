<?php

namespace Zwuiix\Handler;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class CustomServer
{
    use SingletonTrait;

    public function getPlayerByName(string $name) : ?Player{
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if(stripos($player->getDisplayName(), $name) === 0){
                $curDelta = strlen($player->getDisplayName()) - strlen($name);
                if($curDelta < $delta){
                    $found = $player;
                    $delta = $curDelta;
                }
                if($curDelta === 0){
                    break;
                }
            }
        }
        return $found;
    }
}