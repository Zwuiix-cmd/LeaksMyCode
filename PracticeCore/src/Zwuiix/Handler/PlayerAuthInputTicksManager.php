<?php

namespace Zwuiix\Handler;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class PlayerAuthInputTicksManager
{
    use SingletonTrait;
    private array $ticks = [];

    /**
     * @param Player $player
     * @return bool
     */
    public function existPlayer(Player $player): bool
    {
        return isset($this->ticks[$player->getName()]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addPlayer(Player $player): void
    {
        if (!$this->existPlayer($player)) {
            $this->ticks[$player->getName()] = 0;
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayer(Player $player): void
    {
        if ($this->existPlayer($player)) {
            unset($this->ticks[$player->getName()]);
        }
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addTick(Player $player): void
    {
        if(!$this->existPlayer($player)){
            return;
        }
        if(is_null($this->ticks[$player->getName()])){
            return;
        }
        $this->ticks[$player->getName()]=$this->getTicks($player)+1;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function setTick(Player $player, int $tick): void
    {
        if(!$this->existPlayer($player)){
            return;
        }
        if(is_null($this->ticks[$player->getName()])){
            return;
        }
        $this->ticks[$player->getName()]=$tick;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getTicks(Player $player): int
    {
        if($this->existPlayer($player)){
            return $this->ticks[$player->getName()];
        }
        return 0;
    }

}