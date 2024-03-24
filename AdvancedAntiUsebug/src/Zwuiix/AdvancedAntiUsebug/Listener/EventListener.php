<?php

namespace Zwuiix\AdvancedAntiUsebug\Listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;

class EventListener implements Listener
{
    public function __construct(
        protected Config $config
    ) {
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     * @priority LOW
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player=$event->getPlayer();
        $block=$event->getBlock();
        if(!$event->isCancelled())return;
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)return;
        foreach ($this->config->get("block-ids") as $id){
            if($block->getId() !== $id)continue;
            $event->getPlayer()->setRotation($event->getPlayer()->getLocation()->getYaw(),90);
            $event->getPlayer()->teleport($event->getPlayer()->getLocation());
            $event->cancel();
            return;
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     * @priority LOW
     */
    public function onBreak(BlockBreakEvent $event): void
    {
        $player=$event->getPlayer();
        $block=$event->getBlock();
        if(!$event->isCancelled())return;
        foreach ($this->config->get("block-ids") as $id){
            if($block->getId() !== $id)continue;
            $event->getPlayer()->setRotation($event->getPlayer()->getLocation()->getYaw(),90);
            $event->getPlayer()->teleport($event->getPlayer()->getLocation());
            $event->cancel();
            return;
        }
    }
}