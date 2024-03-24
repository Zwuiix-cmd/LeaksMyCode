<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\phase;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class PhaseA extends Module
{
    public function __construct()
    {
        parent::__construct("Phase", "A",
            ModuleManager::generateDefaultData(
                "Checks if a player makes an invalid movement into a block",
                10, ["back" => "instant"]
            ));
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if ($packet instanceof PlayerAuthInputPacket) {
            if($session->getPlayer()->isCreative()) {
                return;
            }
            if(is_null($session->lastNoSuffocateLocation)) {
                $session->back("instant");
                return;
            }

            if($session->isInsideOfSolid($session->currentLocation) && $session->teleportTicks >= 15 && $session->isInLoadedChunk && !$session->isInVoid) {
                if(count($session->pressedKeys) > 0) {
                    if(($distance = $session->currentLocation->distance($session->lastNoSuffocateLocation)) >= 5) {
                        $session->flag($this, ["keys=" . implode(',', $session->pressedKeys), "distance={$distance}"]);
                    }
                    $session->back("instant", $session->lastNoSuffocateLocation);
                }
            }
        }
    }
}