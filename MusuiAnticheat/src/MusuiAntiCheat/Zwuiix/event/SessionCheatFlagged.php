<?php

namespace MusuiAntiCheat\Zwuiix\event;

use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class SessionCheatFlagged extends Event implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        protected Session $session,
        protected Module $module,
        protected array $details
    ) {}

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}