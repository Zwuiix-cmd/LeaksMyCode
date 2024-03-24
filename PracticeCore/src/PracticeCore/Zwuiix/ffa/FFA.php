<?php

namespace PracticeCore\Zwuiix\ffa;

use pocketmine\world\World;
use PracticeCore\Zwuiix\kit\Kit;
use PracticeCore\Zwuiix\session\Session;

class FFA
{
    public function __construct(
        protected string $name,
        protected string $itemTexture,
        protected Kit $kit,
        protected World $world,
        protected bool $build,
        protected bool $antiInterrupt
    ) {}

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
    public function getItemTexture(): string
    {
        return $this->itemTexture;
    }

    /**
     * @return Kit
     */
    public function getKit(): Kit
    {
        return $this->kit;
    }

    /**
     * @return World
     */
    public function getWorld(): World
    {
        return $this->world;
    }

    /**
     * @return bool
     */
    public function hasBuild(): bool
    {
        return $this->build;
    }

    /**
     * @return bool
     */
    public function hasAntiInterrupt(): bool
    {
        return $this->antiInterrupt;
    }

    /**
     * @return int
     */
    public function getPlayers(): int
    {
        return count($this->getWorld()->getPlayers());
    }

    /**
     * @param Session $session
     * @return void
     */
    public function addPlayer(Session $session): void
    {
        if($session->isInFFA()) return;
        $session->setFfa($this);
        $this->getKit()->give($session);
        $session->teleport($this->getWorld()->getSafeSpawn());
    }
}