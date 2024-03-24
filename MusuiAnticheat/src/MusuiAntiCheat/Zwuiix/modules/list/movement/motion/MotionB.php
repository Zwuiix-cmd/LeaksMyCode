<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\motion;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class MotionB extends Module
{
    public function __construct()
    {
        parent::__construct("Motion", "B",
            ModuleManager::generateDefaultData(
                "It allows you to detect if a person is flying through the air without permission.",
                15, ["maxDiff" => 0.6, "maxYDiff" => 0.5, "back" => "smooth"]
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
            // Private
        }
    }
}