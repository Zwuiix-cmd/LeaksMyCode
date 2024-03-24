<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\fly;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\MovementConstants;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class FlyB extends Module
{
    private const THRESHOLD = 1 / 16;

    public function __construct()
    {
        parent::__construct("Fly", "B",
            ModuleManager::generateDefaultData(
                "It allows you to detect if a person is flying through the air without permission.",
                15, ["maxDiff" => 0.65, "minBuffer" => 4, "maxBuffer" => 8, "subtractBuffer" => 0.15, "back" => "smooth"]
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
        if ($packet instanceof PlayerAuthInputPacket && $session->lastDeathTicks >= 40 &&  $session->getPlayer()->getInAirTicks() > 20 && $session->motionTicks >= 45 && $session->teleportTicks >= 15 && !$session->getPlayer()->onGround && $session->flyingTicks >= 10 && $session->glidingTicks >= 3 && !$session->getPlayer()->getAllowFlight()) {
            // Private
        }
    }
}