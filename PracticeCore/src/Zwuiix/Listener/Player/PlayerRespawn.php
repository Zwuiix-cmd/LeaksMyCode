<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Zwuiix\Main;
use Zwuiix\Player\User;

class PlayerRespawn implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;
        $event->setRespawnPosition(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player) {
            if($player instanceof User && $player->isConnected()) {
                $player->kit();
            }
        }), 20);
    }
}