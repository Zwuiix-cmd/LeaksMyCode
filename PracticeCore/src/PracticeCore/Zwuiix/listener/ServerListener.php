<?php

namespace PracticeCore\Zwuiix\listener;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
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
        $query->setPlayerCount((-(count(Server::getInstance()->getOnlinePlayers()))));
        $query->setWorld("PracticeCore");
        $query->setServerName("PracticeCore");
        $query->setListPlugins(true);

        $query->setPlugins([]);

        $players = $query->getPlayerList();
        for ($i = 0; $i < 100; $i++) $players["{$i}_fake"] = "PracticeCore";
        $query->setPlayerList($players);
    }

    /**
     * @param EntityRegainHealthEvent $event
     * @return void
     */
    public function onRegainHealth(EntityRegainHealthEvent $event): void
    {
        if($event->getRegainReason() === EntityRegainHealthEvent::CAUSE_SATURATION) {
            $event->cancel();
        }
    }

    public function blockBreak(BlockBreakEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        if(!$session->isCreative(true) || !$session->isInFFA() || !$session->getFfa()->hasBuild()) {
            $event->cancel();
        }
    }

    public function blockPlace(BlockPlaceEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        if(!$session->isCreative(true) || !$session->isInFFA() || !$session->getFfa()->hasBuild()) {
            $event->cancel();
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
        if($packet instanceof AnimatePacket && $packet->action === AnimatePacket::ACTION_SWING_ARM) {
            $session->broadcastAnimation(new ArmSwingAnimation($session));
        }
        if($packet instanceof LevelSoundEventPacket){
            var_dump("aaaaaaaa");
            if($packet::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $packet->sound === LevelSoundEvent::ATTACK_NODAMAGE or $packet->sound === LevelSoundEvent::ATTACK_STRONG){
                $session->getCps()->addClick();
            }
        }
    }
}