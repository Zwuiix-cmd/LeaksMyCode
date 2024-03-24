<?php

namespace Zwuiix\Listener\Player;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Tasks\BanVerification;
use Zwuiix\Tasks\IPVerification;

class PlayerJoin implements Listener
{

    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     * @throws Exception
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;
        $event->setJoinMessage("§a+ {$player->getName()}");
        $player->spawn();


        if(!Main::getInstance()->playersdata->exists($player->getXuid())) {
            $player->setFirstPlayed();
        }
    }
}