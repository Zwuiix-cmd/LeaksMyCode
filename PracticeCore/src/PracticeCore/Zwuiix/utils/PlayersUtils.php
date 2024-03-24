<?php

namespace PracticeCore\Zwuiix\utils;

use pocketmine\network\mcpe\convert\TypeConverter;
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
            $onlinePlayer->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($session->getUniqueId(), $session->getId(), $customName ?? $session->getDisplayName(), TypeConverter::getInstance()->getSkinAdapter()->toSkinData($session->getSkin()), !is_null($customName) ? "" : $session->getXuid())]));
        }
    }
}
