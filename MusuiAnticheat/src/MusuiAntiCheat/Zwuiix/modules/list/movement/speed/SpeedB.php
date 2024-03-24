<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\movement\speed;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class SpeedB extends Module
{
    public function __construct()
    {
        parent::__construct("Speed", "B",
            ModuleManager::generateDefaultData(
                "Detects whether a person is running at an abnormal speed, or too fast.",
                20, ["multiplicate" => 0.5, "maxSpeed" => 1.13, "back" => "smooth"]
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
            if($session->constantCheck() && !$session->getPlayer()->getAllowFlight()) {
                $level = 0;
                if($session->getPlayer()->getEffects()->has(VanillaEffects::SPEED())) {
                    $effect = $session->getPlayer()->getEffects()->get(VanillaEffects::SPEED());
                    $level = $effect->getEffectLevel();
                }

                $multiplicate = 1 + ($level * $this->options("multiplicate", 0.5));
                $lastPos = new Vector2($session->lastLocation->x, $session->lastLocation->z);
                $newPos = new Vector2($packet->getPosition()->x, $packet->getPosition()->z);

                $distance = $newPos->distance($lastPos);

                $speed = $this->options("maxSpeed", 1.13);
                if($distance > ($speed * $multiplicate)) {
                    $diff = abs($distance - ($speed * $multiplicate));
                    $session->flag($this, ["type=friction", "diff={$diff}", "dist={$distance}", "multiplicate={$multiplicate}", "ping={$session->getNetwork()->getPing()}ms"], true);
                }
            }
        }
    }
}