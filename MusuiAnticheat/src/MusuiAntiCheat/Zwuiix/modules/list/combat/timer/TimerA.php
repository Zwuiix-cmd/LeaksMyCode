<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\timer;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class TimerA extends Module
{
    public function __construct()
    {
        parent::__construct("Timer", "A",
            ModuleManager::generateDefaultData(
                "Allows you to check whether a player is playing faster or slower than usual.",
                2,
                ["timeDiff" => 60, "precision" => 2, "balance" => -5]
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
        // TODO: https://github.com/ethaniccc/Esoteric/blob/master/src/ethaniccc/Esoteric/check/misc/timer/TimerA.php
        if(!$packet instanceof PlayerAuthInputPacket) return;
        if(!$session->getPlayer()->isAlive()) {
            $session->lastPacketReceive = null;
            return;
        }

        $currentTime = microtime(true) * 1000;
        if($session->lastPacketReceive === null){
            $session->lastPacketReceive = $currentTime;
            return;
        }

        $timeDiff = round(($currentTime - $session->lastPacketReceive) / $this->options("timeDiff", 60), $this->options("precision", 2));
        $session->balance -= 1;
        $session->balance += $timeDiff;

        if($session->balance <= $this->options("balance", -5)){
            $this->detect();
            $session->flag($this, ["type=balance", "timediff={$timeDiff}", "diff={$session->balance}", "ping={$session->getNetwork()->getPing()}ms"], true);
            $session->balance = 0;
        }
        $session->lastPacketReceive = $currentTime;
    }
}