<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\airjump;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use ReflectionException;

class AirJumpA extends Module
{
    public function __construct()
    {
        parent::__construct("AirJump", "A",
            ModuleManager::generateDefaultData(
                "Allows you to check whether a player can make infinite jumps, thus stealing.",
                5, ["maxDoubleJumpTicks" => 8]
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
            if(
                $packet->hasFlag(PlayerAuthInputFlags::START_JUMPING) &&
                $session->lastJumpTicks < $this->options("maxDoubleJumpTicks", 8) &&
                !$session->getPlayer()->onGround
                && !$session->isInVoid &&
                $session->isInLoadedChunk &&
                $session->lastGroundTick >= 3 &&
                $session->lastAttackTicks >= 45 &&
                $session->teleportTicks >= 15
            ) {
                $session->flag($this, ["type=double_jump", "diff={$session->lastJumpTicks}", "ping={$session->getNetwork()->getPing()}ms"], true);
            }
        }
    }
}