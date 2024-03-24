<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use Zwuiix\Blocks\tile\CrateTile;
use Zwuiix\Handler\Protection;
use Zwuiix\Player\User;

class PlayerInteract implements Listener
{
    public function onBlockTouch(PlayerInteractEvent $event) : void
    {
        $player = $event->getPlayer();
        if(!$player instanceof User)return;

        $event->cancel();
    }
}