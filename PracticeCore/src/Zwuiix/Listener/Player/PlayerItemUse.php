<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use Zwuiix\Handler\Protection;
use Zwuiix\Interface\GUI\StaffGui;
use Zwuiix\Items\CustomItem;
use Zwuiix\Player\User;

class PlayerItemUse implements Listener
{
    public function onUse(PlayerItemUseEvent $event)
    {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;
        $item=$player->getInventory()->getItemInHand();
    }
}