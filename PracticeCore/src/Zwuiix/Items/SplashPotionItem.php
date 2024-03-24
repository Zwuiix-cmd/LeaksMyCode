<?php

declare(strict_types=1);

namespace Zwuiix\Items;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\PotionType;
use pocketmine\item\SplashPotion;
use pocketmine\player\Player;
use Zwuiix\Entity\projectile\Potion;

class SplashPotionItem extends SplashPotion
{

    private PotionType $potionType;

    public function __construct(ItemIdentifier $identifier, string $name, PotionType $potionType, protected int $max = 1)
    {
        parent::__construct($identifier, $name, $potionType);
        $this->potionType = $potionType;
    }

    public function getThrowForce(): float
    {
        return 0.5;
    }

    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new Potion($location, $thrower, $this->potionType);
    }

    /**
     * @return PotionType
     */
    public function getPotionType(): PotionType
    {
        return $this->potionType;
    }

    public function getMaxStackSize(): int
    {
        return $this->max;
    }
}