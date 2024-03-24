<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\fly;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\MovementConstants;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use ReflectionException;

class FlyA extends Module
{
    public function __construct()
    {
        parent::__construct("Fly", "A",
            ModuleManager::generateDefaultData(
                "It allows you to detect if a person is flying through the air without permission.",
                1
            ));
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof PlayerAuthInputPacket) {
            if(is_null($session->lastPlayerAuthInputFlags)) {
                return;
            }
            $flying = MovementConstants::resolveOnOffInputFlags($session->lastPlayerAuthInputFlags, PlayerAuthInputFlags::START_FLYING, PlayerAuthInputFlags::STOP_FLYING);
            if ($flying && !$session->getPlayer()->getAllowFlight() && !$session->getPlayer()->isFlying() && !$session->inventoryClose && $session->getPlayer()->spawned && $session->teleportTicks >= 15) {
                $session->flag($this, ["allowFlight=false"], true);
            }
        }
    }
}