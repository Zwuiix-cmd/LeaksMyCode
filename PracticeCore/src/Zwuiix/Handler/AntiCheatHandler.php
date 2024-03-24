<?php

namespace Zwuiix\Handler;

use pocketmine\utils\SingletonTrait;
use Zwuiix\Player\User;

class AntiCheatHandler
{
    use SingletonTrait;

    protected ?TimerCheatHandler $timerCheatHandler = null;
    protected ?CPSCheatHandler $CPSCheatHandler = null;

    /**
     * @return TimerCheatHandler
     */
    public function getTimerHandler(): TimerCheatHandler
    {
        return $this->timerCheatHandler ?? $this->timerCheatHandler=new TimerCheatHandler();
    }

    /**
     * @return CPSCheatHandler
     */
    public function getCPSHandler(): CPSCheatHandler
    {
        return $this->CPSCheatHandler ?? $this->CPSCheatHandler=new CPSCheatHandler();
    }

    /**
     * @param User $user
     * @param bool $str
     * @return void
     */
    public function initializePlayer(User $user, bool $str): void
    {
        $timerHandler=$this->getTimerHandler();
        $cpsHandler=$this->getCPSHandler();

        if($str){
            $timerHandler->addPlayer($user);
        }else $timerHandler->removePlayer($user);

        if($str){
            $cpsHandler->addPlayer($user);
        }else $cpsHandler->removePlayer($user);
    }
}

class TimerCheatHandler
{
    private array $authInputPackets = [];

    /**
     * @param User $player
     * @return bool
     */
    public function existPlayer(User $player): bool
    {
        return isset($this->authInputPackets[$player->getName()]);
    }

    /**
     * @param User $player
     * @return void
     */
    public function addPlayer(User $player): void
    {
        if (!$this->existPlayer($player)) {
            $this->authInputPackets[$player->getName()] = [];
        }
    }

    /**
     * @param User $player
     * @return void
     */
    public function removePlayer(User $player): void
    {
        if ($this->existPlayer($player)) {
            unset($this->authInputPackets[$player->getName()]);
        }
    }

    /**
     * @param User $player
     * @return void
     */
    public function addPacket(User $player): void
    {
        if(!$this->existPlayer($player)){
            return;
        }
        if(is_null($this->authInputPackets[$player->getName()]))  return;
        array_unshift($this->authInputPackets[$player->getName()], microtime(true));
        if (count($this->authInputPackets[$player->getName()]) > 500) {array_pop($this->authInputPackets[$player->getName()]);}
    }

    /**
     * @param User $player
     * @param float $deltaTime
     * @param int $roundPrecision
     * @return float
     */
    public function getPackets(User $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (!$this->existPlayer($player) or empty($this->authInputPackets[$player->getName()])) {return 0.0;}
        $mt = microtime(true);
        return round(count(array_filter($this->authInputPackets[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {return ($mt - $t) <= $deltaTime;})) / $deltaTime, $roundPrecision);
    }

    /**
     * @param User $player
     * @param float $deltaTime
     * @param int $roundPrecision
     * @return float
     */
    public function getDirectPackets(User $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (!$this->existPlayer($player) or empty($this->authInputPackets[$player->getName()])) {return 0.0;}
        $mt = microtime(true);
        return count(array_filter($this->authInputPackets[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {return ($mt - $t) <= $deltaTime;})) / $deltaTime;
    }
}

class CPSCheatHandler
{
    private array $clicks = [];

    /**
     * @param User $player
     * @return bool
     */
    public function existPlayer(User $player): bool
    {
        return isset($this->clicks[$player->getName()]);
    }

    /**
     * @param User $player
     * @return void
     */
    public function addPlayer(User $player): void
    {
        if (!$this->existPlayer($player)) {
            $this->clicks[$player->getName()] = [];
        }
    }

    /**
     * @param User $player
     * @return void
     */
    public function removePlayer(User $player): void
    {
        if ($this->existPlayer($player)) {
            unset($this->clicks[$player->getName()]);
        }
    }

    /**
     * @param User $player
     * @return void
     */
    public function addClick(User $player): void
    {
        if(!$this->existPlayer($player)){
            return;
        }
        if(is_null($this->clicks[$player->getName()])){
            return;
        }
        array_unshift($this->clicks[$player->getName()], microtime(true));

        $player->sendTip("§9{$player->getCps()}");
        $player->addGlobalCPS($player->getCps());
    }

    /**
     * @param User $player
     * @param float $deltaTime
     * @param int $roundPrecision
     * @return float
     */
    public function getCps(User $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (!$this->existPlayer($player) or empty($this->clicks[$player->getName()])) {return 0.0;}
        $mt = microtime(true);
        return round(count(array_filter($this->clicks[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {return ($mt - $t) <= $deltaTime;})) / $deltaTime, $roundPrecision);
    }
}
