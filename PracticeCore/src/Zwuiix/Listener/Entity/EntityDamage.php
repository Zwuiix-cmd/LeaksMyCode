<?php

namespace Zwuiix\Listener\Entity;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use Zwuiix\Config\Message;
use Zwuiix\Entity\vanilla\CustomVanillaEntity;
use Zwuiix\Entity\vanilla\MobStacker;
use Zwuiix\Handler\DurabilityHandler;
use Zwuiix\Handler\Protection;
use Zwuiix\Interface\GUI\FreezeGui;
use Zwuiix\Items\CustomItem;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Utils\Marcel;

class EntityDamage implements Listener
{
    private array $cooldownAttack = array();
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    public function onHurt(EntityDamageEvent $event) : void{
        $player = $event->getEntity();
        $cause = $event->getCause();
        if(!$player instanceof User)return;

        if($cause === EntityDamageEvent::CAUSE_VOID){
            $player->teleport($player->getWorld()->getSpawnLocation());
            $event->cancel();
            return;
        }

        if ($cause === EntityDamageEvent::CAUSE_FALL) {
            $event->cancel();
            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $damager=$event->getDamager();
            if($damager instanceof User) {

                $item=$damager->getInventory()->getItemInHand();
                if($player->isImmobile() or $damager->isImmobile()) $event->cancel();

                if (!$event->isCancelled()){
                    $player->setLastDamagePosition($damager->getPosition());
                    $event->setAttackCooldown(9.9806);
                    
                    Marcel::getInstance()->hasReach($damager, $player, $event);
                }
            }
        }
    }
}