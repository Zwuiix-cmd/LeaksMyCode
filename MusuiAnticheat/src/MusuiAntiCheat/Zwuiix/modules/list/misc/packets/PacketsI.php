<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use ReflectionException;

class PacketsI extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "I",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
                ["xuid" => true]
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
        if($packet instanceof BookEditPacket) {
            if ($packet->type == BookEditPacket::TYPE_SIGN_BOOK) {
                if ($this->options("xuid", true) && ($xuid = $session->getPlayer()->getXuid()) !== $packet->xuid) {
                    $session->flag($this, ["xuid={$xuid}", "send={$packet->xuid}"]);
                }
            }
        }
    }
}