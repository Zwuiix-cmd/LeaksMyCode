<?php

namespace PlutooCore\utils;

class EffectInstance extends \pocketmine\entity\effect\EffectInstance
{
    private int $amplifier;

    public function getAmplifier(): int
    {
        return $this->amplifier;
    }

    public function setAmplifier(int $amplifier): \pocketmine\entity\effect\EffectInstance
    {
        $this->amplifier = $amplifier;
        return $this;
    }

}