<?php

namespace AdvancedSpawner\Zwuiix\spawner;

use pocketmine\item\ItemBlock;

class Spawner
{
    public function __construct(
        protected string    $name,
        protected string    $entityId,
        protected ItemBlock $block,
        protected int       $spawnRadius,
        protected int       $spawnDistance,
        protected int       $spawnSpeed,
        protected array     $drops,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return ItemBlock
     */
    public function getBlock(): ItemBlock
    {
        return $this->block;
    }

    /**
     * @return int
     */
    public function getSpawnRadius(): int
    {
        return $this->spawnRadius;
    }

    /**
     * @return int
     */
    public function getSpawnDistance(): int
    {
        return $this->spawnDistance;
    }

    /**
     * @return int
     */
    public function getSpawnSpeed(): int
    {
        return $this->spawnSpeed;
    }

    /**
     * @return array
     */
    public function getDrops(): array
    {
        return $this->drops;
    }
}