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
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\item\SplashPotion;
use PracticeCore\Zwuiix\kit\NodebuffKit;
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
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        $session->spawn();
        $session->sendMessage(LanguageHandler::getInstance()->translate("welcome"));
        $event->setJoinMessage("");
    }

    /**
     * @param PlayerRespawnEvent $event
     * @return void
     */
    public function onRespawn(PlayerRespawnEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        $event->setRespawnPosition(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        PracticeCore::getInstance()->getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use($session) {
            if($session->isConnected()) $session->spawn();
        }), 10);
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onSessionQuit(PlayerQuitEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;

        if($session->getCooldownByName(Session::TAG_COMBAT_LOGGER_COOLDOWN)->isInCooldown()) {
            $lastCause = $session->getLastDamageCause();
            if($lastCause instanceof EntityDamageByEntityEvent && !is_null($lastCause->getDamager())) {
                $session->attack(new EntityDamageByEntityEvent($session, $lastCause->getDamager(),EntityDamageEvent::CAUSE_SUICIDE, 1000));
            } else $session->attack(new EntityDamageEvent($session, EntityDamageEvent::CAUSE_SUICIDE, 1000));
        }

        $event->setQuitMessage("");
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onSessionChat(PlayerChatEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;

        $message = $event->getMessage();
        $format = $session->getInfo()->getChatFormatter();

        if(!$session->hasPermission("practicecore.session.chat_restricted")) {
            if(
                str_contains(strtolower($message), "@here") ||
                strtolower($message) == strtolower($session->getLastMessage()) ||
                str_starts_with(strtolower($message), strtolower($session->getLastMessage()))
            ) {
                $session->sendMessage($format->format($session->getDisplayName(), $message));
                $event->cancel();
                return;
            }

            if(strlen($message) < 3){
                $session->sendMessage(LanguageHandler::getInstance()->translate("chat_error_two_characters"));
                $event->cancel();
                return;
            }

            $chatCooldown = $session->getCooldownByName(Session::TAG_CHAT_COOLDOWN);
            if($chatCooldown->isInCooldown()) {
                $session->sendMessage(LanguageHandler::getInstance()->translate("chat_spam", [($chatCooldown->getCooldown() / 20)]));
                $event->cancel();
                return;
            }

            $session->setLastMessage($message);
            $chatCooldown->setCooldown(true, false, 20 * 2);
        }

        $event->setFormatter($format);
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
        $cmd = $event->getCommand();
        $command = explode(" ", $cmd);
        $command[0] = strtolower($command[0]);
        $cmd = implode(" ", $command);
        $event->setCommand($cmd);

        $sender = $event->getSender();
        if($sender instanceof Session) {
            if($sender->getCooldownByName(Session::TAG_COMBAT_LOGGER_COOLDOWN)->isInCooldown() && !Server::getInstance()->isOp($sender->getName())) {
                $event->cancel();
                $sender->sendMessage("§cSorry, you're in combat, you can't place this order!");
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        if(!$session->isCreative(true) || !$session->isInFFA() || !$session->getFfa()->hasBuild()) {
            $event->cancel();
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if($entity instanceof Session && $damager instanceof Session && !$event->isCancelled()) {
            if(!$entity->isInFFA()) {
                $event->cancel();
                return;
            }

            if($entity->getFfa()->hasAntiInterrupt()) {
                if($entity->hasOpponent()) {
                    if($entity->getOpponent()->getUniqueId() !== $damager->getUniqueId()) {
                        $event->cancel();
                        return;
                    }
                } else {
                    $entity->setOpponent($damager);
                    $damager->setOpponent($entity);
                }

                $sessions=[$entity, $damager];
                foreach ($sessions as $session) {
                    if(!$session instanceof Session) continue;
                    $cooldown = $session->getCooldownByName(Session::TAG_COMBAT_LOGGER_COOLDOWN);
                    $cooldown->setCooldown(true, !$cooldown->isInCooldown(), 20 * 30);
                }
            }

            $entity->setLastDamageVector($damager->getPosition()->asVector3());
            $event->setAttackCooldown(KnockbackHandler::getInstance()->getAttackCooldown());
        }
    }

    public function onSessionDeath(PlayerDeathEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;

        $cause = $session->getLastDamageCause()->getCause();
        if ($cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
            $damager=$event->getPlayer()->getLastDamageCause()->getDamager();
            if(!$damager instanceof Session)return;

            $session->broadcastAnimation(new DeathAnimation($session), $session->getViewers());
            $session->knockBack($session->getPosition()->getX() - $damager->getPosition()->getX(), $session->getPosition()->getZ() - $damager->getPosition()->getZ(), 0.59, 0.32);
            $session->effectKill($damager);

            $event->setDeathMessage($format = LanguageHandler::getInstance()->translate("death_by_player", [
                $damager->getDisplayName(), $damager->countItems(SplashPotion::getInstance()),
                $session->getDisplayName(), $session->countItems(SplashPotion::getInstance()),
            ]));
            $event->setDeathScreenMessage($format);

            NodebuffKit::getInstance()->give($damager);
            $damager->getCooldownByName(Session::TAG_COMBAT_LOGGER_COOLDOWN)->setCooldown(false, false);
            $damager->getCooldownByName(Session::TAG_ENDER_PEARL_COOLDOWN)->setCooldown(false, false);
            $damager->setOpponent(null);
            $damager->getInfo()->addKill();
            $damager->getInfo()->addKillStreak();
            $damager->getXpManager()->setXpLevel(0);
            $damager->getXpManager()->setXpProgress(0);
        }
        $event->setDrops([]);
        $event->setXpDropAmount(0);
    }

    public function onDamage(EntityDamageEvent $event) : void
    {
        $session = $event->getEntity();
        $cause = $event->getCause();
        if (!$session instanceof Session) return;

        if(!$event->isCancelled() && $event->isApplicable($event::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN)) $event->cancel();
        if ($cause === EntityDamageEvent::CAUSE_VOID) {
            $session->teleport($session->getWorld()->getSpawnLocation());
            $event->cancel();
            return;
        }
        if ($cause === EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
            return;
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     * @return void
     */
    public function onSessionDropItem(PlayerDropItemEvent $event): void
    {
        $session = $event->getPlayer();
        if(!$session instanceof Session) return;
        $event->cancel();
    }
}