<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\event\PacketReceiveAsyncEvent;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use ReflectionException;

class PacketsF extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "F",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
            ));
    }

    /**
     * @param Session $session
     * @param mixed $packet
     * @param mixed|null $event
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof ModalFormResponsePacket && $event instanceof DataPacketReceiveEvent) {
            // Il bug donc bof
        }
    }

    public function callOutbound(Session $session, ClientboundPacket $packet, DataPacketSendEvent $event): void
    {
        if($packet instanceof ModalFormRequestPacket) {
            $data = json_decode($packet->formData, true);
            if(!is_null($data)) {
                $session->addForm($packet->formId, $data);
            }
        }
    }
}