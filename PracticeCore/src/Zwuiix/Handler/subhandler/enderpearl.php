<?php

namespace Zwuiix\Handler\subhandler;

use Zwuiix\Main;
use Zwuiix\Player\User;

class enderpearl
{

    public function __construct(private User $player)
    {
    }

    public function setCooldown(bool $value = true, bool $notify = true, int $time = 300): void
    {
        if($value){
            Main::getInstance()->pearlPlayer[$this->player->getName()]=time() + $time;
            if ($notify) {
                $this->player->sendActionBarMessage('§cCooldown a commencé');
            }
        }else{
            Main::getInstance()->pearlPlayer[$this->player->getName()]=time() + -1;
            if ($notify) {
                $this->player->sendActionBarMessage('§aCooldown terminé');
            }
        }
    }

    public function isInCooldown(): bool
    {
        if (isset(Main::getInstance()->pearlPlayer[$this->player->getName()])) {
            if (Main::getInstance()->pearlPlayer[$this->player->getName()] > time()) {
                return true;
            }
        }
        return false;
    }

    public function getCooldown(): int
    {
        return Main::getInstance()->pearlPlayer[$this->player->getName()] - time();
    }
}