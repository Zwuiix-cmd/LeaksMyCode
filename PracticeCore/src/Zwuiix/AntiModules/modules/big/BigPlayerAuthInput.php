<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigPlayerAuthInput extends Module
{
    

    public function getName(): string
    {
        return "BigPlayerAuthInput";
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
        if(!$packet instanceof PlayerAuthInputPacket)return false;
        return ($packet->getBlockActions() !== null && count($packet->getBlockActions()) >= ModuleManager::MAX_BLOCK_ACTION) || ($packet->getItemInteractionData() !== null && count($packet->getItemInteractionData()->getRequestChangedSlots()) >= ModuleManager::MAX_ITEM_INTERACTION);
    }
}