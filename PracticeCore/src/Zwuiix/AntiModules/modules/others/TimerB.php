<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Player\User;

class TimerB extends Module
{
    

    public function getName(): string
    {
        return "TimerB";
    }

    public function getDescription(): string
    {
        return "Detect Timer";
    }

    public function getType(): string
    {
        return "B";
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof PlayerAuthInputPacket)return false;
        $packetsCount = AntiCheatHandler::getInstance()->getTimerHandler()->getDirectPackets($user);

        if($user->timerAWait <= 8){
            $user->timerAWait++;
            return false;
        }

        $v=22;
        if($user->spawned && $packetsCount > $v){
            $user->timerAWait=0;
            $user->timerViolations++;
            $this->violations[$user->getName()]="{$packetsCount}/$v}";
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