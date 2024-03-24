<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;

class PrimarineJS extends Module
{
    

    public function getName(): string
    {
        return "PrimarineJS";
    }

    public function getDescription(): string
    {
        return "Detect PrimarineJS";
    }

    public function getType(): string
    {
        return "A";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        return $user->spawned && $user->getPlayerInfo()->getExtraData()["DeviceModel"] === "PrismarineJS";
    }
}