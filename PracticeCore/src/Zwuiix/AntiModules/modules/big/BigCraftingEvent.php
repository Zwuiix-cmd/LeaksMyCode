<?php

namespace Zwuiix\AntiModules\modules\big;

use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\AntiModules\ModuleManager;
use Zwuiix\Player\User;

class BigCraftingEvent extends Module
{
    

    public function getName(): string
    {
        return "BigCraftingEvent";
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
        if(!$packet instanceof CraftingEventPacket)return false;
        return count($packet->input) > ModuleManager::MAX_CRAFTING_INPUT || count($packet->output) > ModuleManager::MAX_CRAFTING_OUTPUT;
    }
}