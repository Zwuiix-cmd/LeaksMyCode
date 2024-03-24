<?php

namespace PracticeCore\Zwuiix\trait;

use pocketmine\Server;
use PracticeCore\Zwuiix\listener\RecordListener;
use PracticeCore\Zwuiix\listener\ServerListener;
use PracticeCore\Zwuiix\listener\SessionListener;

trait ListenerTrait
{
    /**
     * @return void
     */
    public function loadListener(): void
    {
        Server::getInstance()->getPluginManager()->registerEvents(new SessionListener(), $this->getPlugin());
        Server::getInstance()->getPluginManager()->registerEvents(new ServerListener(), $this->getPlugin());
    }
}