<?php

namespace Zwuiix\Handler;

use Zwuiix\Handler\subhandler\all;
use Zwuiix\Handler\subhandler\batontp;
use Zwuiix\Handler\subhandler\bow;
use Zwuiix\Handler\subhandler\citrouille;
use Zwuiix\Handler\subhandler\enderpearl;
use Zwuiix\Handler\subhandler\Focus;
use Zwuiix\Handler\subhandler\hight;
use Zwuiix\Handler\subhandler\Logs;
use Zwuiix\Handler\subhandler\near;
use Zwuiix\Handler\subhandler\PotionsLuncheur;
use Zwuiix\Handler\subhandler\star;
use Zwuiix\Player\User;

class Cooldown
{

    public function __construct(public User $player)
    {
    }

    protected enderpearl $enderpearl;

    public function enderpearl() : enderpearl
    {
        $this->enderpearl=new enderpearl($this->player);
        return $this->enderpearl;
    }
}