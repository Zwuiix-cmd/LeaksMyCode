<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigText extends Module
{
    

    public function getName(): string
    {
        return "BigText";
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
        if(!$packet instanceof TextPacket)return false;
        return count($packet->parameters) >= ModuleManager::MAX_TEXT_PARAMETERS;
    }
}