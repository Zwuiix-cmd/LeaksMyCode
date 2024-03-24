<?php

namespace PracticeCore\Zwuiix\session;

class CooldownTick extends Cooldown
{
    /**
     * @return void
     */
    public function reduceSmoothCooldown():  void
    {
        if($this->existCooldown()) $this->cooldown--;
    }
}