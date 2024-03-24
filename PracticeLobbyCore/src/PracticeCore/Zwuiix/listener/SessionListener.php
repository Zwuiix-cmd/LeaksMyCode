<?php

namespace PracticeCore\Zwuiix\listener;

use pocketmine\entity\animation\DeathAnimation;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use PracticeCore\Zwuiix\form\ServersForm;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\item\SplashPotion;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class SessionListener implements Listener
{
    /**
     * @param PlayerCreationEvent $event
     * @return void
     */
    public function onClientCreate(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(Session::class);
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onSessionJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof Session) return;
        $event->setJoinMessage("");
        ServersForm::getInstance()->send($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onSessionQuit(PlayerQuitEvent $event): void
    {
        $event->setQuitMessage("");
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onSessionChat(PlayerChatEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param PlayerExhaustEvent $event
     * @return void
     */
    public function onExhaust(PlayerExhaustEvent $event): void
    {
        $event->cancel();
        $event->getPlayer()->getHungerManager()->setFood(20);
        $event->getPlayer()->getHungerManager()->setSaturation(20);
    }

    public function onCommand(CommandEvent $event): void
    {
        $event->cancel();
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $event->cancel();
    }

    public function onSessionDeath(PlayerDeathEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        $event->setDeathMessage("");
        $event->setDrops([]);
        $event->setXpDropAmount(0);
    }

    public function onDamage(EntityDamageEvent $event) : void
    {
        $event->cancel();
    }

    /**
     * @param PlayerDropItemEvent $event
     * @return void
     */
    public function onSessionDropItem(PlayerDropItemEvent $event): void
    {
        $event->cancel();
    }
}