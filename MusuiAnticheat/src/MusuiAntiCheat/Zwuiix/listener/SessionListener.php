<?php

namespace MusuiAntiCheat\Zwuiix\listener;

use JsonException;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;

class SessionListener implements Listener
{
    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $player = $event->getEntity();
        if(!$player instanceof Player) return;
        $session = SessionManager::getInstance()->getSession($player);
        $session->lastAttackTicks = 0;
    }

    public function onEntityTeleport(EntityTeleportEvent $event): void
    {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;
        $session = SessionManager::getInstance()->getSession($entity);
        $session->teleportTicks = 0;
    }

    public function onEntityMotion(EntityMotionEvent $event): void
    {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) return;
        $session = SessionManager::getInstance()->getSession($entity);
        $session->motionTicks = 0;
        $session->lastMotion = $event->getVector();
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     * @throws JsonException
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        SessionManager::getInstance()->construct($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        SessionManager::getInstance()->destruct($player);
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onDeath(PlayerDeathEvent $event): void
    {
        $session = SessionManager::getInstance()->getSession($event->getPlayer());
        $session->lastDeathTicks = 0;
    }

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player=$event->getPlayer();
        $info = $player->getNetworkSession()->getPlayerInfo();
        if(!$info instanceof PlayerInfo){
            $player->kick(LanguageHandler::getInstance()->translate("error_login_handle"), false);
            return;
        }
    }
}