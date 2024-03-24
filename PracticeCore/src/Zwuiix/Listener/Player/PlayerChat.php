<?php

namespace Zwuiix\Listener\Player;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use Zwuiix\Handler\AFK;
use Zwuiix\Handler\Rank\RankHandler;
use Zwuiix\handler\Sanction;
use Zwuiix\Handler\ServerChat;
use Zwuiix\Player\User;
use Zwuiix\Utils\Permissions;

class PlayerChat implements Listener {

    public array $cooldown = [];

    const COOLDOWN_TIME = 3;

    /**
     * @throws Exception
     */
    public function playerChat(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        if(!$player instanceof User)return;

        $message=TextFormat::clean($event->getMessage());
        if(str_contains(strtolower($event->getMessage()), "@here")) {
            $event->cancel();
            return;
        }

        $isOp = $player->isOp();
        $color = $isOp ? "§c" : "§a";
        $event->setFormat($color . "{$player->getName()}: {$message}");

        if (!isset($this->cooldown[$event->getPlayer()->getName()])) $this->cooldown[$event->getPlayer()->getName()] = time();
        if (time() < $this->cooldown[$event->getPlayer()->getName()]) {
            $event->cancel();
            $name = $event->getPlayer();
            $second = $this->cooldown[$event->getPlayer()->getName()] - time();
            $name->sendMessage("§cDésolé, vous devez attendre $second seconde(s).");
        } else if(!$isOp){
            $this->cooldown[$event->getPlayer()->getName()] = time() + 2;
        }
    }
}