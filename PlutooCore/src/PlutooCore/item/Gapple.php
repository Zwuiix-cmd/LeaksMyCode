<?php

namespace PlutooCore\item;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\GoldenApple;

class Gapple extends GoldenApple
{
    public function getAdditionalEffects() : array{
        return [
            new EffectInstance(VanillaEffects::REGENERATION(), 100, 0),
            new EffectInstance(VanillaEffects::ABSORPTION(), 2400)
        ];
    }
}