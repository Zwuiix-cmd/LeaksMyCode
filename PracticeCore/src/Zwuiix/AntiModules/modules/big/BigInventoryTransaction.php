<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigInventoryTransaction extends Module
{
    

    public function getName(): string
    {
        return "BigInventoryTransaction";
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
        if(!$packet instanceof InventoryTransactionPacket) return false;
        return count($packet->trData->getActions()) >= ModuleManager::MAX_INVENTORY_TRANSACTION;
    }
}