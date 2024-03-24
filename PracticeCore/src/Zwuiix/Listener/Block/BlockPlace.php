<?php

namespace Zwuiix\Listener\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use Zwuiix\Handler\Protection;
use Zwuiix\Main;
use Zwuiix\Player\User;

class BlockPlace implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    public function onBlockPlace(BlockPlaceEvent $event) : void{
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if(!$player instanceof User)return;
        $event->cancel();
    }
}