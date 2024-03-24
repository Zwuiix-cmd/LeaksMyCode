<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use ReflectionException;

class PacketsG extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "G",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                10,
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
        // Il false
    }

    /**
     * @param Session $session
     * @param ClientboundPacket $packet
     * @param DataPacketSendEvent $event
     * @return void
     */
    public function callOutbound(Session $session, ClientboundPacket $packet, DataPacketSendEvent $event): void
    {
        if($packet instanceof ContainerOpenPacket) {
            $session->inventoryClose = false;
        }
    }
}