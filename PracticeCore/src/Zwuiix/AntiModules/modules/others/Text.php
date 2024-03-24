<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;

class Text extends Module
{
    

    public function getName(): string
    {
        return "Text";
    }

    public function getDescription(): string
    {
        return "Detect Text sending";
    }

    public function getType(): string
    {
        return "A";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof TextPacket)return false;
        return ($packet->xboxUserId !== $user->getXuid() || $packet->needsTranslation || $packet->sourceName !== $user->getName());
    }
}