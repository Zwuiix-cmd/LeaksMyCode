<?php

declare(strict_types=1);

namespace Zwuiix\Items;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\EnderPearl;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EnderPearlItem extends EnderPearl
{
    public function getThrowForce(): float
    {
        return 2.35;
    }

    protected function createEntity(Location $location, User|Player $thrower): Throwable
    {
        return new Pearl($location, $thrower);
    }

    public function onClickAir(User|Player $player, Vector3 $directionVector): ItemUseResult
    {
        if (!$player->isCreative()) {
            if ($player->getCooldown()->enderpearl()->isInCooldown()) {
                return ItemUseResult::FAIL();
            } else $player->getCooldown()->enderpearl()->setCooldown();
        }

        return parent::onClickAir($player, $directionVector);
    }
}