<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\proxy;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\event\player\PlayerJoinEvent;
use ReflectionException;

class ProxyA extends Module
{
    public function __construct()
    {
        parent::__construct("Proxy", "A",
            ModuleManager::generateDefaultData(
                "Allows you to check if the client is sending false connection information, i.e. if he's not a real human player.",
                1,
                [
                    "allowPrismarineJS" => false,
                    "allowToolbox" => false
                ]
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
        if($packet instanceof PlayerJoinEvent) {
            $deviceOs = $session->getUserInfo()->getPlayerPlatform();
            $deviceModel = $session->getUserInfo()->getDeviceModel();
            if(!$this->options("allowPrismarineJS", false) && $deviceModel === "PrismarineJS") {
                $session->flag($this, ["type=prismarine_client", "devicemodel={$deviceModel}", "ping={$session->getNetwork()->getPing()}ms"]);
            }
            if(!$this->options("allowToolbox", false) && $deviceOs === "Android" && $deviceModel !== strtoupper($deviceModel)) {
                $session->flag($this, ["type=toolbox_client", "deviceos={$deviceOs}", "devicemodel={$deviceModel}", "ping={$session->getNetwork()->getPing()}ms"]);
            }
        }
    }
}