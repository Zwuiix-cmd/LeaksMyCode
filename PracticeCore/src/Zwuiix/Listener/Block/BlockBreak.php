<?php

namespace Zwuiix\Listener\Block;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\SmokeParticle;
use Zwuiix\Handler\Protection;
use Zwuiix\Main;
use Zwuiix\Player\User;

class BlockBreak implements Listener
{
    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin = $main;
    }

    public function onBlockBreak(BlockBreakEvent $event) : void{
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if(!$player instanceof User)return;

        $event->cancel();
        $player->getWorld()->addParticle($block->getPosition()->add(0.5, 0.8, 0.5), new BlockBreakParticle($block), [$player]);
        $player->getWorld()->addParticle($block->getPosition()->add(0.5, 1, 0.5), new SmokeParticle(0), [$player]);
    }
}