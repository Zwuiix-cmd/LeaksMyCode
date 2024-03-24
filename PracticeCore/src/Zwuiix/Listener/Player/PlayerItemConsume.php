<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use Zwuiix\Handler\Protection;

class PlayerItemConsume implements Listener
{
    /**
     * @throws \JsonException
     */
    public function onConsume(PlayerItemConsumeEvent $event): void
    {
        $player=$event->getPlayer();
        $item=$player->getInventory()->getItemInHand();

        if($event->isCancelled())return;
    }
}