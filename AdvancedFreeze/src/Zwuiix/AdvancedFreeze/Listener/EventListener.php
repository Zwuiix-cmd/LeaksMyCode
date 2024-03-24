<?php

namespace Zwuiix\AdvancedFreeze\Listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use Zwuiix\AdvancedFreeze\Handler\FreezeHandler;
use Zwuiix\AdvancedFreeze\Main;
use Zwuiix\AdvancedFreeze\Utils\Utils;

class EventListener implements Listener
{
    public function onChat(PlayerChatEvent $event)
    {
        $player=$event->getPlayer();
        if($event->getMessage() === "f"){
            $event->cancel();
            FreezeHandler::getInstance()->setFrozen($player, !FreezeHandler::getInstance()->isFrozen($player));
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $player=$event->getPlayer();
        if(FreezeHandler::getInstance()->isFrozen($player)) $event->cancel();
    }

    public function onEntityDamage(EntityDamageEvent $event)
    {
        $entity=$event->getEntity();
        if(!$entity instanceof Player)return;
        if(!FreezeHandler::getInstance()->isFrozen($entity)) return;
        $event->cancel();

        if(!$event instanceof EntityDamageByEntityEvent) return;

        $damager=$event->getDamager();
        if(!$damager instanceof Player)return;

        $damager->sendMessage(Main::getInstance()->getData()->getNested("messages.player-frozen"));
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player=$event->getPlayer();
        FreezeHandler::getInstance()->setFrozen($player, FreezeHandler::getInstance()->isFrozen($player));
    }
}