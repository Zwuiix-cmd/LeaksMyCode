<?php

namespace Zwuiix\AntiModules\modules\others;

use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\AntiModules\Module;
use Zwuiix\Player\User;

class TimerA extends Module
{
    protected array $lastTime = array();
    protected array $balance = array();

    public function getName(): string
    {
        return "TimerA";
    }

    public function getDescription(): string
    {
        return "Detect TimerA";
    }

    public function getType(): string
    {
        return "A";
    }

    /**
     * @return bool
     */
    public function isBannable(): bool
    {
        return true;
    }

    public function detect(User $user, ServerboundPacket $packet): bool
    {
        if(!$packet instanceof PlayerAuthInputPacket)return false;
        if(!$user->isAlive()){
            $this->lastTime[$user->getName()]=null;
            $this->balance[$user->getName()]=0;
            return false;
        }

        $ping=$user->getNetworkSession()->getPing();
        if(abs($ping - $user->lastPing) <= 25) {
            return false;
        }

        $currentTime = microtime(true) * 1000;
        if(!isset($this->lastTime[$user->getName()])) {
            $this->lastTime[$user->getName()] = $currentTime;
        }
        if(!isset($this->balance[$user->getName()])) {
            $this->balance[$user->getName()] = 0;
        }

        if (is_null($this->lastTime[$user->getName()])) {
            $this->lastTime[$user->getName()] = $currentTime;
            return false;
        }

        $timeDiff = round(($currentTime - $this->lastTime[$user->getName()]) / 50, 2);

        $this->balance[$user->getName()] -= 1;
        $this->balance[$user->getName()] += $timeDiff;

        if ($this->balance[$user->getName()] <= -5) {
            $this->balance[$user->getName()] = 0;
            return true;
        }

        $this->lastTime[$user->getName()] = $currentTime;
        return false;
    }
}