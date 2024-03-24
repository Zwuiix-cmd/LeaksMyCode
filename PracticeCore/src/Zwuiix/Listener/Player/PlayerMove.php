<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use Zwuiix\Handler\event\list\Koth;
use Zwuiix\Main;
use Zwuiix\Player\User;

class PlayerMove implements Listener
{

    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @throws \Exception
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;
    }
}