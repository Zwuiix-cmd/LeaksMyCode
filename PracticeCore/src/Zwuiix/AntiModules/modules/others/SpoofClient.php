<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;
use Zwuiix\Utils\PacketUtils;

class SpoofClient extends Module
{
    

    public function getName(): string
    {
        return "SpoofClient";
    }

    public function getDescription(): string
    {
        return "Detect Fake Client information";
    }

    public function getType(): string
    {
        return "A";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof LoginPacket)return false;
        $authData = PacketUtils::fetchAuthData($packet->chainDataJwt);
        $clientData = PacketUtils::parseClientData($packet->clientDataJwt);
        $playerOs = $clientData->DeviceOS;
        $titleID = $authData->titleId;
        $givenOS = $playerOs;
        $expectedOS = match ($titleID) {
            "896928775" => DeviceOS::WINDOWS_10,
            "2047319603" => DeviceOS::NINTENDO,
            "1739947436" => DeviceOS::ANDROID,
            "2044456598" => DeviceOS::PLAYSTATION,
            "1828326430" => DeviceOS::XBOX,
            "1810924247" => DeviceOS::IOS,
            default => "Unknown",
        };
        return $user->spawned && $expectedOS !== $givenOS;
    }
}