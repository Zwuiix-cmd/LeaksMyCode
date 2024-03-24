<?php

namespace PracticeCore\Zwuiix\item;

use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier as IID;
use pocketmine\item\ItemTypeIds as Ids;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\enchantment\FakeEnchantment;
use PracticeCore\Zwuiix\form\FFAForm;
use PracticeCore\Zwuiix\form\SettingsForm;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\session\Session;

class Settings extends Item
{
    use SingletonTrait;

    public function __construct()
    {
        parent::__construct(new IID(Ids::BOOK), "Book");
        $this->addEnchantment(FakeEnchantment::getInstance()->getEnchantmentInstance());
        $this->setCustomName(LanguageHandler::getInstance()->translate("settings_item"));
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
        if($player instanceof Session) {
            SettingsForm::getInstance()->send($player);
        }
        return ItemUseResult::SUCCESS();
    }

    /**
     * @return int
     */
    public function getCooldownTicks(): int
    {
        return 20;
    }
}