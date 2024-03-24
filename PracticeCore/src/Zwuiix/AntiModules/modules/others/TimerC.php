<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Player\User;

class TimerC extends Module
{
    public function getName(): string
    {
        return "TimerC";
    }

    public function getDescription(): string
    {
        return "Detect Timer";
    }

    public function getType(): string
    {
        return "C";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof PlayerAuthInputPacket)return false;
        $packetsCount = AntiCheatHandler::getInstance()->getTimerHandler()->getDirectPackets($user);
        $ping=$user->getNetworkSession()->getPing();

        if($user->timerBWait <= 8){
            $user->timerBWait++;
            return false;
        }

        $v=21;
        if($user->spawned && $packetsCount > $v) {
            $user->timerViolations++;
            $user->timerBWait=0;
            $this->violations[$user->getName()]="{$packetsCount}/$v";
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasLog(): bool
    {
        return true;
    }
}