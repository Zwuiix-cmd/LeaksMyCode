<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\motion;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\player\Player;
use ReflectionException;

class MotionA extends Module
{
    public function __construct()
    {
        parent::__construct("Motion", "A",
            ModuleManager::generateDefaultData(
                "It allows you to detect if a person is flying through the air without permission.",
                20, ["airDiff" => 0.624, "highDiff" => 0.582, "back" => "smooth"]
            ));
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof PlayerAuthInputPacket) {
            if($session->lastAttackTicks >= 45 && $session->lastDeathTicks >= 40 && $session->motionTicks >= 45 && $session->teleportTicks >= 25) {
                $rawPos = $packet->getPosition();
                $newPos = $rawPos->round(4)->subtract(0, 1.62, 0);
                if ($packet->hasFlag(PlayerAuthInputFlags::START_JUMPING)) {
                    if ($this->checkAirJump($session->getPlayer(), $newPos)) {
                        $diff = abs($newPos->getFloorY() - ($session->getPlayer()->getPosition()->getFloorY() + $session->getPlayer()->getJumpVelocity() + $this->options("airDiff", 0.624)));
                        $session->flag($this, ["diff={$diff}", "ping={$session->getNetwork()->getPing()}ms"], true);
                    }
                    $session->jumped++;
                } else if ($packet->hasFlag(PlayerAuthInputFlags::CHANGE_HEIGHT)) {
                    $session->jumped = 1;
                } else {
                    $session->jumped = 0;
                }
                if ($session->jumped !== 1) {
                    $rawPos = $packet->getPosition();
                    $newPos = $rawPos->round(4)->subtract(0, 1.62, 0);
                    if ($this->checkHighJump($session->getPlayer(), $newPos)) {
                        $diff = abs($newPos->getFloorY() - ($session->getPlayer()->getPosition()->getFloorY() + $session->getPlayer()->getJumpVelocity() + $this->options("highDiff", 0.582)));
                        $session->flag($this, ["type=vertical", "diff={$diff}", "ping={$session->getNetwork()->getPing()}ms"], true);
                    }
                }
            }
        }
    }

    public function checkHighJump(Player $player, Vector3 $newPos): bool{
        return $player->isSurvival()  && !$player->isUnderwater()  &&(!$player->getEffects()->has(VanillaEffects::LEVITATION()) && $newPos->getFloorY() >  ($player->getPosition()->getFloorY() +  $player->getJumpVelocity() + 0.582));
    }

    public function checkAirJump(Player $player, Vector3 $newPos): bool{
        return $player->isSurvival() && !$player->isUnderwater() && (!$player->isOnGround()  || (!$player->getEffects()->has(VanillaEffects::LEVITATION()) && $newPos->getFloorY() > ($player->getPosition()->getFloorY() + $player->getJumpVelocity() +  0.624)));
    }
}