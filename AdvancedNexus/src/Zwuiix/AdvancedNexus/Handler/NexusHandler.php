<?php

namespace Zwuiix\AdvancedNexus\Handler;

use pocketmine\utils\SingletonTrait;
use Zwuiix\AdvancedNexus\Entities\NexusEntity;
use Zwuiix\AdvancedNexus\Lib\xenialdan\apibossbar\BossBar;

class NexusHandler
{
    use SingletonTrait;

    private bool $nexus = false;
    protected NexusEntity $entity;
    protected ?BossBar $bossBar = null;

    /**
     * @return bool
     */
    public function isNexus(): bool
    {
        return $this->nexus;
    }

    /**
     * @param bool $resp
     * @return void
     */
    public function setNexus(bool $resp): void
    {
        $this->nexus=$resp;
    }

    /**
     * @return NexusEntity
     */
    public function getEntity(): NexusEntity
    {
        return $this->entity;
    }

    /**
     * @param NexusEntity $entity
     */
    public function setEntity(NexusEntity $entity): void
    {
        $this->entity = $entity;
    }

    public function createBossBar(): void
    {
        $this->bossBar=new BossBar();
    }

    /**
     * @return BossBar|null
     */
    public function getBossBar(): ?BossBar
    {
        return $this->bossBar;
    }
}