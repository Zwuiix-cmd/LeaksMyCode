<?php

namespace PracticeCore\Zwuiix\listener;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\handler\ServersHandler;
use PracticeCore\Zwuiix\MultiProtocol;
use PracticeCore\Zwuiix\session\Session;

class ServerListener implements Listener
{
    /**
     * @param QueryRegenerateEvent $event
     * @return void
     */
    public function onQueryRegenerate(QueryRegenerateEvent $event): void
    {
        $query = $event->getQueryInfo();
        $query->setMaxPlayerCount(1);
        $query->setPlayerCount((-(ServersHandler::getInstance()->getAllPlayers())));
        $query->setWorld(LanguageHandler::getInstance()->translate("server_name"));
        $query->setServerName(LanguageHandler::getInstance()->translate("server_name"));
        $query->setListPlugins(true);
        $query->setPlugins([]);
        $query->setPlayerList([]);
    }

    /**
     * @param EntityRegainHealthEvent $event
     * @return void
     */
    public function onRegainHealth(EntityRegainHealthEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function blockBreak(BlockBreakEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function blockPlace(BlockPlaceEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param LeavesDecayEvent $event
     * @return void
     */
    public function leavesDecay(LeavesDecayEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param BlockBurnEvent $event
     * @return void
     */
    public function blockBurn(BlockBurnEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param BlockFormEvent $event
     * @return void
     */
    public function blockForm(BlockFormEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param EntityTrampleFarmlandEvent $event
     * @return void
     */
    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param BlockGrowEvent $event
     * @return void
     */
    public function onBlockGrow(BlockGrowEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param PlayerPreLoginEvent $event
     * @return void
     */
    public function onPreLogin(PlayerPreLoginEvent $event): void
    {
        if(Server::getInstance()->getNetwork()->getValidConnectionCount() > Server::getInstance()->getMaxPlayers()){
            $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, LanguageHandler::getInstance()->translate("server_full"));
            return;
        }
        if(!Server::getInstance()->isWhitelisted($event->getPlayerInfo()->getUsername())){
            $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, LanguageHandler::getInstance()->translate("server_whitelisted"));
            return;
        }
        if(Server::getInstance()->getNameBans()->isBanned($event->getPlayerInfo()->getUsername()) || Server::getInstance()->getIPBans()->isBanned($event->getIp())){
            $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, LanguageHandler::getInstance()->translate("server_banned"));
            return;
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    public function onDataReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $session = ($networkSession = $event->getOrigin())->getPlayer();
        if(!$session instanceof Session) return;
    }

    /**
     * @param DataPacketSendEvent $event
     * @return void
     */
    public function onDataSend(DataPacketSendEvent $event): void
    {
        $packets = $event->getPackets();
        foreach ($packets as $i => $packet) {
            if($packet instanceof StartGamePacket) {
                $packet->worldName = LanguageHandler::getInstance()->translate("server_name");
            }
        }
    }
}