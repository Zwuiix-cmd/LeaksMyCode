<?php

namespace Zwuiix\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zwuiix\Main;
use Zwuiix\Player\User;

class EnderPearlTask extends Task
{
    public function onRun(): void
    {
        if (!empty(Main::getInstance()->pearlPlayer)) {
            foreach (Main::getInstance()->pearlPlayer as $name => $time) {
                $player = Server::getInstance()->getPlayerExact($name);
                if (!$player instanceof User) continue;

                $time=$player->getCooldown()->enderpearl()->getCooldown();

                if ($time <= 0) {
                    if ($player->getCooldown()->enderpearl()->isInCooldown()) {
                        $player->getCooldown()->enderpearl()->setCooldown(false);
                    }
                    $player->getXpManager()->setXpAndProgress(0, 0);
                } else {
                    $percent = floatval($time / 300);
                    $player->getXpManager()->setXpAndProgress(intval($time / 20), $percent);
                    Main::getInstance()->pearlPlayer[$name]--;
                }
            }
        }
    }
}