<?php

namespace PracticeCore\Zwuiix\utils;

use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\session\Session;

class PlayersUtils
{
    use SingletonTrait;

    /**
     * @param Session $session
     * @return void
     */
    public function removeOnlinePlayer(Session $session): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->getUniqueId() === $session->getUniqueId()) continue;
            $onlinePlayer->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($session->getUniqueId())]));
        }
    }

    /**
     * @param Session $session
     * @param string|null $customName
     * @return void
     */
    public function addOnlinePlayer(Session $session, ?string $customName = null): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->getUniqueId() === $session->getUniqueId()) continue;
            $onlinePlayer->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($session->getUniqueId(), $session->getId(), $customName ?? $session->getDisplayName(), SkinAdapterSingleton::get()->toSkinData($session->getSkin()), "")]));
        }
    }

    /**
     * @param string $name
     * @return Session|null
     */
    public function getPlayerByPrefix(string $name) : ?Session{
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if(!$player instanceof Session) continue;
            $pName = strtolower($player->isDisguise() ? $player->getDisguiseName() : $player->getName());
            if(stripos($pName, $name) === 0){
                $curDelta = strlen($pName) - strlen($name);
                if($curDelta < $delta){
                    $found = $player;
                    $delta = $curDelta;
                }
                if($curDelta === 0) break;
            }
        }

        return $found;
    }
}
