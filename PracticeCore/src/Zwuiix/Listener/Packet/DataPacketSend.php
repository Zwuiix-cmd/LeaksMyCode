<?php

namespace Zwuiix\Listener\Packet;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

class DataPacketSend implements Listener
{
    public function dataPacket(DataPacketSendEvent $event)
    {
        $packets = $event->getPackets();
        $target=$event->getTargets();
        foreach ($packets as $packet) {
            if ($packet::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID and $packet instanceof LevelSoundEventPacket) {
                if ($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE or $packet->sound === LevelSoundEvent::ATTACK_STRONG) {
                    $event->cancel();
                }
            }
        }
    }
}