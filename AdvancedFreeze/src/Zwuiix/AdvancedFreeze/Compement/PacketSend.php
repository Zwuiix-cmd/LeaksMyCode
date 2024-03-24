<?php

namespace Zwuiix\AdvancedFreeze\Compement;

use Zwuiix\AdvancedFreeze\Compement\ResourcePackManager;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;

class PacketSend
{
    /**
     * @var ClientboundPacket[]
     */
    private array $packets = [];
    private NetworkSession $session;

    /**
     * @param NetworkSession $session
     */
    public function __construct(NetworkSession $session)
    {
        $this->session = $session;
    }

    /**
     * @param ClientboundPacket $packet
     * @return void
     */
    public function addPacket(ClientboundPacket $packet): void
    {
        $this->packets[] = $packet;
    }

    /**
     * @return void
     */
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