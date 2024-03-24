<?php

namespace Zwuiix\Listener\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Tasks\LuncherTask;
use Zwuiix\Tasks\UsernameTask;
use Zwuiix\Utils\Custom;

class PlayerQuit implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player=$event->getPlayer();
        $name=$player->getName();
        if(!$player instanceof User)return;
        $event->setQuitMessage("§c- {$player->getName()}");
        AntiCheatHandler::getInstance()->initializePlayer($player, false);
    }
}