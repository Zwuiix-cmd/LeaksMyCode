<?php

namespace PracticeCore\Zwuiix\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class ScheduleUpdateTask extends Task
{
    public function __construct(
        protected \PracticeCore\Zwuiix\server\Server $server
    ) {}

    public function onRun(): void
    {
        Server::getInstance()->getAsyncPool()->submitTask(new ServerPlayersAsyncTask($this->server->toStringAddress(), $this->server->getAddress(), $this->server->getPort()));
    }
}