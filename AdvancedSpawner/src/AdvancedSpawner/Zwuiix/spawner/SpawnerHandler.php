<?php

namespace AdvancedSpawner\Zwuiix\spawner;

use pocketmine\utils\SingletonTrait;

class SpawnerHandler
{
    use SingletonTrait;

    /**
     * @var Spawner[]
     */
    protected array $spawners = [];

    /**
     * @param string $name
     * @return Spawner|null
     */
    public function getSpawnerByName(string $name): ?Spawner
    {
        return $this->spawners[strtolower($name)] ?? null;
    }

    /**
     * @param Spawner $spawner
     * @return void
     */
    public function register(Spawner $spawner): void
    {
        $this->spawners[strtolower($spawner->getName())] = $spawner;
    }

    /**
     * @return Spawner[]
     */
    public function getAll(): array
    {
        return $this->spawners;
    }
}