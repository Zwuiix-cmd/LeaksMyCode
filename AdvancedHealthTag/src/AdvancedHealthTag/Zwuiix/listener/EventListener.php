<?php

namespace AdvancedHealthTag\Zwuiix\listener;

use AdvancedHealthTag\Zwuiix\handler\HealthTagHandler;
use AdvancedHealthTag\Zwuiix\utils\FormatValueColor;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;

class EventListener implements Listener
{
    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }

        HealthTagHandler::getInstance()->updatePlayer($player);
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        HealthTagHandler::getInstance()->updatePlayer($player);
    }

    public function onEntityRegen(EntityRegainHealthEvent $event)
    {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }

        HealthTagHandler::getInstance()->updatePlayer($entity);
    }
}