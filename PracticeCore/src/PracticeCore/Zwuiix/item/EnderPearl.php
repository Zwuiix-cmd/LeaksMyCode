<?php

namespace PracticeCore\Zwuiix\item;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\EnderPearl as EnderPearlPM;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\entities\EnderPearlProjectile;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\session\Session;

class EnderPearl extends EnderPearlPM
{
    use SingletonTrait;

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::ENDER_PEARL), "EnderPearl");
        $this->setCustomName(LanguageHandler::getInstance()->translate("practice_item"));
        CreativeInventory::getInstance()->add($this);
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @param array $returnedItems
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        if(!$player instanceof Session) return ItemUseResult::FAIL();

        if (!$player->isCreative()) {
            $cooldown = $player->getCooldownByName(Session::TAG_ENDER_PEARL_COOLDOWN);
            if ($cooldown->isInCooldown()) return ItemUseResult::FAIL();
            $cooldown->setCooldown(true, true, 300);
        }

        return parent::onClickAir($player, $directionVector, $returnedItems);
    }

    protected function createEntity(Location $location, Player $thrower) : Throwable
    {
        return new EnderPearlProjectile($location, $thrower);
    }

    /**
     * @return int
     */
    public function getCooldownTicks(): int
    {
        return 5;
    }
}