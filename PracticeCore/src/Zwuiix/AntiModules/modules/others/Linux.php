<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;

class Linux extends Module
{
    

    public function getName(): string
    {
        return "Linux";
    }

    public function getDescription(): string
    {
        return "Detect Linux";
    }

    public function getType(): string
    {
        return "A";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        $pData=$user->getPlayerInfo();
        return $user->spawned && $pData->getExtraData()['DeviceOS'] === DeviceOS::ANDROID && $pData->getExtraData()["DeviceModel"] === "";
    }
}