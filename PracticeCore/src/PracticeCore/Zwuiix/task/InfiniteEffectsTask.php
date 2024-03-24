<?php

namespace PracticeCore\Zwuiix\task;

use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class InfiniteEffectsTask extends Task
{
    public function __construct()
    {
        PracticeCore::getInstance()->getPlugin()->getScheduler()->scheduleRepeatingTask($this, 3);
    }

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if (!$onlinePlayer instanceof Session) continue;
            foreach ($onlinePlayer->getEffects()->getInfiniteEffects() as $effect) {
                $onlinePlayer->getNetworkSession()->sendDataPacket(MobEffectPacket::add($onlinePlayer->getId(), true, EffectIdMap::getInstance()->toId($effect->getType()), $effect->getAmplifier(), $effect->isVisible(), 20 * 14));
                $onlinePlayer->getNetworkSession()->sendDataPacket(MobEffectPacket::add($onlinePlayer->getId(), true, EffectIdMap::getInstance()->toId($effect->getType()), $effect->getAmplifier(), $effect->isVisible(), 20 * 15));
            }
        }
    }
}