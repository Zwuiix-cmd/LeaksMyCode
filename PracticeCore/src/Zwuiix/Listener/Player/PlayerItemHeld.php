<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use Zwuiix\Main;
use Zwuiix\Player\User;

class PlayerItemHeld implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    public function onHeld(PlayerItemHeldEvent $event) : void{
        $player = $event->getPlayer();
        $item=$event->getItem();
        if(!$player instanceof User)return;
    }
}