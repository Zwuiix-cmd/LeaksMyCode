<?php

namespace PracticeCore\Zwuiix\handler;

use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\server\Server;

class ServersHandler
{
    use SingletonTrait;

    /*** @var Server[] */
    protected array $servers = [];

    /**
     * @param Server $server
     * @return void
     */
    public function register(Server $server): void
    {
        if(isset($this->servers[$server->toStringAddress()])) return;
        $this->servers[$server->toStringAddress()] = $server;
    }

    /**
     * @param int $id
     * @return Server|null
     */
    public function getServerById(int $id): ?Server
    {
        $servers = [];
        foreach ($this->getAll() as $server) $servers[] = $server;
        return $servers[$id] ?? null;
    }

    /**
     * @param string $name
     * @return Server|null
     */
    public function getServerByName(string $name): ?Server
    {
        return $this->servers[$name] ?? null;
    }

    /**
     * @return int
     */
    public function getAllPlayers(): int
    {
        $players = count(\pocketmine\Server::getInstance()->getOnlinePlayers());
        foreach ($this->getAll() as $server) {
            $players += $server->getPlayers();
        }

        return $players;
    }

    /**
     * @return Server[]
     */
    public function getAll(): array
    {
        return $this->servers;
    }
}