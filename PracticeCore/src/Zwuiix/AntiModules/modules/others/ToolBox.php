<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;

class ToolBox extends Module
{
    

    public function getName(): string
    {
        return "ToolBox";
    }

    public function getDescription(): string
    {
        return "Detect ToolBox";
    }

    public function getType(): string
    {
        return "A";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        $pData=$user->getPlayerInfo();
        $DeviceOs=$pData->getExtraData()['DeviceOS'];
        $DeviceModel=$pData->getExtraData()["DeviceModel"];
        return $user->spawned && !$DeviceOs == DeviceOS::ANDROID && $DeviceModel !== strtoupper($DeviceModel);
    }
}