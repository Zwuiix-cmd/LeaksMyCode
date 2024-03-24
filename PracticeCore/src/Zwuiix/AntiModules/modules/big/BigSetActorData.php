<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigSetActorData extends Module
{
    

    public function getName(): string
    {
        return "BigSetActorData";
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
        if(!$packet instanceof SetActorDataPacket)return false;
        return count($packet->metadata) >= ModuleManager::MAX_METADATA;
    }
}