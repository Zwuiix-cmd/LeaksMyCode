<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\ClientCacheBlobStatusPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigClientCacheBlobStatus extends Module
{
    

    public function getName(): string
    {
        return "BigClientCacheBlobStatus";
    }

    public function getDescription(): string
    {
        return "Detect big information send to server";
    }

    public function getType(): string
    {
        return "A+";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof ClientCacheBlobStatusPacket) return false;
        return count($packet->getHitHashes()) > ModuleManager::MAX_HIT_HASHES || count($packet->getMissHashes()) > ModuleManager::MAX_MISS_HASHES;
    }
}