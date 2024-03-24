<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\TextPacket;
use ReflectionException;

class PacketsC extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "C",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
                ["parameters" => 50, "xuid" => true, "translation" => true, "sourceName" => true, "type" => true]
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
        if ($packet instanceof TextPacket) {
            if(($parameters = count($packet->parameters)) >= $this->options("parameters", 50)) {
                $session->flag($this, ["sended={$parameters}"]);
            }elseif($this->options("xuid", true) && $packet->xboxUserId !== $session->getPlayer()->getXuid()) {
                $session->flag($this, ["type=xuid", "realXboxUserID={$session->getPlayer()->getXuid()}", "receive={$packet->xboxUserId}", "ping={$session->getNetwork()->getPing()}ms"]);
            }elseif ($this->options("translation", true) && $packet->needsTranslation) {
                $session->flag($this, ["type=translation", "requireTranslation={$packet->needsTranslation}", "ping={$session->getNetwork()->getPing()}ms"]);
            }elseif ($this->options("sourceName", true) && $packet->sourceName !== $session->getPlayer()->getName()) {
                $session->flag($this, ["type=sourceName", "realName={$session->getPlayer()->getName()}", "receive={$packet->sourceName}", "ping={$session->getNetwork()->getPing()}ms"]);
            } elseif ($this->options("type", true) && $packet->type !== TextPacket::TYPE_CHAT) {
                $session->flag($this, ["type={$packet->type}"]);
            }
        }
    }
}