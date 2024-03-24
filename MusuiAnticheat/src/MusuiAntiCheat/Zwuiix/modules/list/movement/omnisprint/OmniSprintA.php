<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\omnisprint;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class OmniSprintA extends Module
{
    public function __construct()
    {
        parent::__construct("OmniSprint", "A",
            ModuleManager::generateDefaultData(
                "Microjang is high and allows players to sprint backwards in this scenario.",
                4
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
        if($packet instanceof PlayerAuthInputPacket){
            $allowedSprinting = $session->isSprinting && in_array('W', $session->pressedKeys);
            if(!$allowedSprinting && count($session->pressedKeys) > 0 && $session->isSprinting){
                $session->flag($this, ["keys=" . implode(',', $session->pressedKeys)], true);
            }
        }
    }
}