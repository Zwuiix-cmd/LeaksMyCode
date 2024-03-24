<?php

namespace PlutooCore\handlers\event;

use Closure;
use pocketmine\Server;

class Event
{
    protected bool $status = false;

    public function __construct(
        protected string $name,
        protected array $times,
        protected Closure $closure,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->status;
    }

    /**
     * @return void
     */
    public function stop(): void
    {
        $this->status = false;
    }

    /**
     * @return array
     */
    public function getTimes(): array
    {
        return $this->times;
    }

    /**
     * @return void
     */
    public function call(): void
    {
        if($this->isStarted()) {
            return;
        }

        Server::getInstance()->broadcastMessage("§aUn évent §9{$this->getName()}§a à démarré! §7(/" . strtolower($this->name) . ")");
        $this->status = true;
        ($this->closure)($this);
    }
}
