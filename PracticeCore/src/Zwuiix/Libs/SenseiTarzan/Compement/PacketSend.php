<?php

namespace Zwuiix\Libs\SenseiTarzan\Compement;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;

class PacketSend
{
    /**
     * @var ClientboundPacket[]
     */
    private array $packets = [];
    private NetworkSession $session;

    public function __construct(NetworkSession $session)
    {
        $this->session = $session;
    }

    public function addPacket(ClientboundPacket $packet)
    {
        $this->packets[] = $packet;
    }


    public function tick(): void
    {
        if (!$this->session->isConnected()) {
            unset(ResourcePackManager::$packSend[$this->session->getDisplayName()]);
            return;
        }

        if ($next = array_shift($this->packets)) {
            $this->session->sendDataPacket($next);
        } else {
            unset(ResourcePackManager::$packSend[$this->session->getDisplayName()]);
        }
    }
}