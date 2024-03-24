<?php

namespace AdvancedSpawner\Zwuiix\task;

use AdvancedSpawner\Zwuiix\tile\MobSpawnerTile;
use pocketmine\scheduler\Task;

class SpawnerTask extends Task
{
    public function __construct(int $period, protected MobSpawnerTile $mobSpawnerTile)
    {
        \AdvancedSpawner::getInstance()->getScheduler()->scheduleRepeatingTask($this, $period);
    }

    public function onRun(): void
    {
        if($this->mobSpawnerTile->closed) {
            $this->getHandler()->cancel();
            return;
        }

        if($this->mobSpawnerTile->canUpdate()) {
            $this->mobSpawnerTile->onUpdate();
        }
    }
}