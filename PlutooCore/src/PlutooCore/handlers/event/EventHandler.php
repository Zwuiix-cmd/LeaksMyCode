<?php

namespace PlutooCore\handlers\event;

use pocketmine\utils\SingletonTrait;

class EventHandler
{
    use SingletonTrait;

    /**
     * @var Event[]
     */
    protected array $events = [];

    /**
     * @param Event $event
     * @return void
     */
    public function register(Event $event): void
    {
        $this->events[strtolower($event->getName())] = $event;
    }

    /**
     * @param string $name
     * @return Event|null
     */
    public function getEventWithName(string $name): ?Event
    {
        return $this->events[strtolower($name)] ?? null;
    }

    /**
     * @return Event[]
     */
    public function getAll(): array
    {
        return $this->events;
    }
}
