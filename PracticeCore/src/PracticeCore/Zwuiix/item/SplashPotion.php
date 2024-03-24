<?php

namespace PracticeCore\Zwuiix\item;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\PotionType;
use pocketmine\item\SplashPotion as SplashPotionPM;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\entities\SplashPotionProjectile;
use PracticeCore\Zwuiix\handler\LanguageHandler;

class SplashPotion extends SplashPotionPM
{
    use SingletonTrait;

    private PotionType $potionType;

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::SPLASH_POTION), "SplashPotion");
        $this->potionType = PotionType::STRONG_HEALING();
        $this->setCustomName(LanguageHandler::getInstance()->translate("practice_item"));
        CreativeInventory::getInstance()->add($this);
    }

    public function getThrowForce(): float
    {
        return 0.5;
    }

    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new SplashPotionProjectile($location, $thrower, $this->potionType);
    }

    public function getType() : PotionType
    {
        return $this->potionType;
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}