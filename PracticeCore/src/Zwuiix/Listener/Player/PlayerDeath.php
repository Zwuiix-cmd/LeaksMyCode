<?php

namespace Zwuiix\Listener\Player;

use JsonException;
use pocketmine\entity\animation\DeathAnimation;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Utils\Utils;

class PlayerDeath implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @throws JsonException
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if(!$player instanceof User)return;
        $event->setDeathMessage("");

        $cause = $player->getLastDamageCause()->getCause();
        if ($cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
            $damager=$event->getPlayer()->getLastDamageCause()->getDamager();
            if(!$damager instanceof User)return;

            $player->broadcastAnimation(new DeathAnimation($player), $player->getViewers());
            $player->knockBack($player->getPosition()->getX() - $damager->getPosition()->getX(), $player->getPosition()->getZ() - $damager->getPosition()->getZ(), 0.59, 0.32);
            Utils::getInstance()->doLightning($player->getLocation(), $damager);

            $event->setDeathMessage("§a{$damager->getDisplayName()}§2[".Utils::getPotionsCount($damager)."] §fa tué §c{$player->getDisplayName()}§4[".Utils::getPotionsCount($player)."]");

            $player->setLastFight($damager);
            $damager->setLastFight($player);

            $damager->kit();
            $damager->addKill();
            $damager->addKillStreak();

            $player->addDeath();
            $player->resetKillStreak();

            $event->setDrops([]);
            $event->setXpDropAmount(0);
        }
    }
}