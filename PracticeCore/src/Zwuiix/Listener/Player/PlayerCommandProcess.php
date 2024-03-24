<?php

namespace Zwuiix\Listener\Player;

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\CommandEvent;
use Zwuiix\Player\User;

class PlayerCommandProcess implements Listener {

    /**
     * @throws Exception
     */
    public function onProcess(PlayerCommandPreprocessEvent $event) : void {
        $player=$event->getPlayer();
        if(!$player instanceof User)return;

        if($event->getMessage()[0] != "/")return;
        $event->setMessage($event->getMessage());
    }

    /**
     * @param CommandEvent $event
     * @priority LOWEST
     */
    public function executeCommand(CommandEvent $event)
    {
        $cmd = $event->getCommand();
        $commande = explode(" ", $cmd);
        $commande[0] = strtolower($commande[0]);
        $cmd = implode(" ", $commande);

        $event->setCommand($cmd);
    }
}