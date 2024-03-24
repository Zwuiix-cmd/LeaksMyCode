<?php

namespace Zwuiix\Libs\SenseiTarzan\Task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Zwuiix\Libs\SenseiTarzan\Compement\RateLimitManager;

class CheckerPacketTask extends Task
{

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            RateLimitManager::getInstance()->resetRateLimitPacket($player->getXuid());
        }
    }
}