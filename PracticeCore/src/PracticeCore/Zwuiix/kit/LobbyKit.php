<?php

namespace PracticeCore\Zwuiix\kit;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\item\FreeForAll;
use PracticeCore\Zwuiix\item\Settings;

class LobbyKit extends Kit
{
    use SingletonTrait;

    public function __construct()
    {
        $inventoryContents = [];
        $inventoryContents[3] = clone FreeForAll::getInstance();
        $inventoryContents[5] = clone Settings::getInstance();

        parent::__construct("Lobby", $inventoryContents, [], [VanillaEffects::NIGHT_VISION()]);
    }
}