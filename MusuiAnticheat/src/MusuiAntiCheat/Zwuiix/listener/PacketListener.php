<?php

namespace MusuiAntiCheat\Zwuiix\listener;

use Exception;
use MusuiAntiCheat\Zwuiix\event\PacketReceiveAsyncEvent;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use MusuiAntiCheat\Zwuiix\utils\PacketUtils;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\SetActorMotionPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\NetworkPermissions;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use pocketmine\network\mcpe\protocol\types\PlayerMovementType;
use pocketmine\player\Player;

class PacketListener implements Listener
{
    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    public function onDataReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $session = ($event->getOrigin())->getPlayer();

        if($packet instanceof LoginPacket) {
            PacketUtils::fetchAuthData($packet->chainDataJwt);
        }

        if($session instanceof Player) {
            $session = SessionManager::getInstance()->getSession($session);
            ModuleManager::getInstance()->callInbound($session, $packet, $event);
        }
    }


    /**
     * @param DataPacketSendEvent $event
     * @return void
     * @throws Exception
     */
    public function onSend(DataPacketSendEvent $event): void
    {
        $packets = $event->getPackets();

        foreach ($packets as $packet) {
            if ($packet instanceof StartGamePacket) {
                $packet->playerMovementSettings = new PlayerMovementSettings(PlayerMovementType::SERVER_AUTHORITATIVE_V2_REWIND, 0, false);
                $packet->networkPermissions = new NetworkPermissions(disableClientSounds: true);
                $packet->levelSettings->muteEmoteAnnouncements = true;
            }
        }

        foreach ($event->getTargets() as $networkSession) {
            $player = $networkSession->getPlayer();

            if(!$player instanceof Player) continue;
            $session = SessionManager::getInstance()->getSession($player);

            foreach ($packets as $packet) {
                ModuleManager::getInstance()->callOutbound($session, $packet, $event);
            }
        }
    }
}