<?php

namespace Zwuiix\AdvancedPurification\Handler;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class PurificationHandlers
{
    use SingletonTrait;

    public function isInZone(Player $player, string $zone)
    {
        $explode = explode(", ", $zone);
        if (($player->getPosition()->x >= min((int)$explode[0], (int)$explode[3])) && ($player->getPosition()->x <= max((int)$explode[0], (int)$explode[3])) &&
            ($player->getPosition()->y >= min((int)$explode[1], (int)$explode[4])) && ($player->getPosition()->y <= max((int)$explode[1], (int)$explode[4])) &&
            ($player->getPosition()->z >= min((int)$explode[2], (int)$explode[5])) && ($player->getPosition()->z <= max((int)$explode[2], (int)$explode[5]))) {
            if ($player->getWorld()->getFolderName() === $explode[6]) {
                return true;
            }
        }
    }
}