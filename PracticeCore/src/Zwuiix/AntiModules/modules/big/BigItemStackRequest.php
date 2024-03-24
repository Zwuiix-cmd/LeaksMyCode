<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigItemStackRequest extends Module
{
    

    public function getName(): string
    {
        return "BigItemStackRequest";
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
        if(!$packet instanceof ItemStackRequestPacket)return false;
        return count($packet->getRequests()) > ModuleManager::MAX_ITEM_STACK_REQUEST;
    }
}