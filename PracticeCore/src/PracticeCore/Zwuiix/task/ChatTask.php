<?php

namespace PracticeCore\Zwuiix\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\CooldownTick;
use PracticeCore\Zwuiix\session\Session;

class ChatTask extends Task
{
    public function __construct()
    {
        PracticeCore::getInstance()->getPlugin()->getScheduler()->scheduleRepeatingTask($this, 1);
    }

    public function onRun() : void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(!$onlinePlayer instanceof Session) continue;
            $cooldown = $onlinePlayer->getCooldownByName(Session::TAG_CHAT_COOLDOWN);
            $time=$cooldown->getCooldown();
            if(!$cooldown->existCooldown()) continue;
            if ($time <= 0) {
                if ($cooldown->isInCooldown()) $cooldown->setCooldown(false, false);
                continue;
            }

            if($cooldown instanceof CooldownTick) $cooldown->reduceSmoothCooldown();
        }
    }
}