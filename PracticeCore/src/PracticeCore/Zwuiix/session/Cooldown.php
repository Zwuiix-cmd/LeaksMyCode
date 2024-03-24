<?php

namespace PracticeCore\Zwuiix\session;

use PracticeCore\Zwuiix\handler\LanguageHandler;

class Cooldown
{
    protected ?int $cooldown = null;

    public function __construct(
        protected Session $session,
    ) {}

    /**
     * @param bool $status
     * @param bool $notify
     * @param int $time
     * @return void
     */
    public function setCooldown(bool $status = true, bool $notify = true, int $time = 30): void
    {
        if($status){
            $this->cooldown = time() + $time;
            if ($notify) $this->session->sendActionBarMessage(LanguageHandler::getInstance()->translate("cooldown_started"));
            return;
        }
        $this->cooldown = null;
        if ($notify) $this->session->sendActionBarMessage(LanguageHandler::getInstance()->translate("cooldown_finished"));
    }

    /**
     * @return bool
     */
    public function isInCooldown(): bool
    {
        return !is_null($this->cooldown) && $this->cooldown >= time();
    }

    public function getCooldown(): int
    {
        return $this->cooldown - time();
    }

    /**
     * @return bool
     */
    public function existCooldown(): bool
    {
        return !is_null($this->cooldown);
    }
}