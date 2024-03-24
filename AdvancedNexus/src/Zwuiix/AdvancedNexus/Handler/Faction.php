<?php

namespace Zwuiix\AdvancedNexus\Handler;

use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class Faction
{
    use SingletonTrait;
    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerFaction(Player $player): string {
        $member = PlayerManager::getInstance()->getPlayer($player);
        $faction = $member?->getFaction();
        if (!is_null($faction)) {
            return $faction->getName();
        } else return "§e...";
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string
    {
        $member = PlayerManager::getInstance()->getPlayer($player);
        $symbol = $member === null ? null : PiggyFactions::getInstance()->getTagManager()->getPlayerRankSymbol($member);
        if ($member === null || $symbol === null) return "";
        return $symbol;
    }

    /**
     * @param Player $player
     * @return FactionsPlayer|null
     */
    public function getPlayerPiggy(Player $player): ?FactionsPlayer
    {
        return PlayerManager::getInstance()->getPlayer($player);
    }
}
