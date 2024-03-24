<?php

namespace Zwuiix\AntiModules;

use pocketmine\network\mcpe\protocol\ServerboundPacket;
use Zwuiix\Player\User;

abstract class Module
{
    /*** @var array */
    public array $violations = array();

    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function getType(): string;

    abstract public function detect(User $user, ServerboundPacket $packet): bool;
    public function getDetectMessage(): string
    {
        return $this->getName() . " [" . $this->getDescription() . "]" . " (" . $this->getType() . ")";
    }

    /**
     * @return bool
     */
    public function hasLog(): bool
    {
        return false;
    }

    /**
     * @return false
     */
    public function isBannable(): bool
    {
        return false;
    }

    protected function reward(User $user, float $sub = 0.01): void
    {
        if(!isset($this->violations[$user->getName()])) $this->violations[$user->getName()]=0;
        $this->violations[$user->getName()] = max($this->violations[$user->getName()] - $sub, 0);
    }
}