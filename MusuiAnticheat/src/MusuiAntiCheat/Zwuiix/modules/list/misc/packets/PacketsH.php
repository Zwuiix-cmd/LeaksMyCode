<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class PacketsH extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "H",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                10,
                ["maxDistance" => 1]
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
        if($packet instanceof PlayerAuthInputPacket) {
            if($session->lastLocationNoClientPredictions === null) return;
            if($session->getPlayer()->hasNoClientPredictions() && ($diff = $session->lastLocationNoClientPredictions->distance($packet->getPosition())) >= $this->options("maxDistance", 1) && $session->constantCheck()) {
                $session->flag($this, ["diff={$diff}"]);
            }
        }
    }
}