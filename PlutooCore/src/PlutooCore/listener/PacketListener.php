<?php

namespace PlutooCore\listener;

use MusuiEssentials\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;
use MusuiEssentials\utils\PacketUtils;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\PacketHandlingException;

class PacketListener
{
    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function onDataReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $session = ($origin = $event->getOrigin())->getPlayer();
        if($packet instanceof LoginPacket) {
            $authData = PacketUtils::fetchAuthData($packet->chainDataJwt);
            $clientData = PacketUtils::parseClientData($packet->clientDataJwt);
            $detectedOs = match ($authData->titleId) {
                "896928775" => DeviceOS::WINDOWS_10,
                "2047319603" => DeviceOS::NINTENDO,
                "1739947436" => DeviceOS::ANDROID,
                "2044456598" => DeviceOS::PLAYSTATION,
                "1828326430" => DeviceOS::XBOX,
                "1810924247" => DeviceOS::IOS,
                default => "Unknown",
            };

            if ($detectedOs !== $clientData->DeviceOS) {
                throw new PacketHandlingException("Invalid TitleID");
            }
            if($detectedOs === DeviceOS::ANDROID && $clientData->DeviceModel !== strtoupper($clientData->DeviceModel)) {
                throw new PacketHandlingException("Invalid DeviceModel");
            }
            if($clientData->ThirdPartyName !== $authData->displayName && $detectedOs !== DeviceOS::PLAYSTATION && $detectedOs !== DeviceOS::NINTENDO) {
                throw new PacketHandlingException("Invalid ThirdPartyName");
            }
            /*if($clientData->SkinImageHeight <= 32) {
                throw new PacketHandlingException("Invalid SkinImageHeight");
            }*/
            if($clientData->PlayFabId === "") {
                throw new PacketHandlingException("Invalid PlayFabId");
            }
            if($clientData->SkinColor === "") {
                throw new PacketHandlingException("Invalid SkinColor");
            }
            if($clientData->CurrentInputMode !== 2 /* PE */ && $clientData->DeviceModel === "SM-G970F") {
                throw new PacketHandlingException("Using bedrocktool");
            }
        }
    }
}