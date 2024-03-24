<?php

namespace PracticeCore\Zwuiix\server;

use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;
use PracticeCore\Zwuiix\task\ScheduleUpdateTask;
use PracticeCore\Zwuiix\task\ServerPlayersAsyncTask;
use pocketmine\Server as PMMP;

class Server
{
    protected ScheduleUpdateTask $updateTask;
    protected int $players = 0;

    public function __construct(
        protected string $name,
        protected string $address,
        protected int    $port,
    ) {
        $this->updateTask = new ScheduleUpdateTask($this);
        PracticeCore::getInstance()->getPlugin()->getScheduler()->scheduleRepeatingTask($this->updateTask, 10);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function toStringAddress(): string
    {
        return strtolower($this->getAddress() . ":" . $this->getPort());
    }

    /**
     * @return int
     */
    public function getPlayers(): int
    {
        return $this->players;
    }

    /**
     * @param int $players
     * @return void
     */
    public function setPlayers(int $players): void
    {
        $this->players = $players;
    }

    /**
     * @param Session $session
     * @return void
     */
    public function transfer(Session $session): void
    {
        $session->getNetworkSession()->transfer($this->getAddress(), $this->getPort());
    }
}