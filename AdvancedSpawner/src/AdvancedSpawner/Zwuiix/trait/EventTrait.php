<?php

namespace AdvancedSpawner\Zwuiix\trait;

use AdvancedSpawner\Zwuiix\entity\MobEntity;
use AdvancedSpawner\Zwuiix\entity\MobStacker;
use AdvancedSpawner\Zwuiix\spawner\SpawnerHandler;
use AdvancedSpawner\Zwuiix\tile\MobSpawnerTile;
use pocketmine\block\tile\Tile;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemBlock;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use ReflectionException;

trait EventTrait
{
    /**
     * @throws ReflectionException
     */
    public function loadEvents(): void
    {
        $this->getServer()->getPluginManager()->registerEvent(BlockPlaceEvent::class, function (BlockPlaceEvent $event) {
            $block = $event->getBlockAgainst();
            $item = $event->getItem();

            if($event->isCancelled()) {
                return;
            }

            if(!$item instanceof ItemBlock) {
                return;
            }
            $tx = $event->getTransaction();
            foreach (SpawnerHandler::getInstance()->getAll() as $spawner) {
                if($spawner->getBlock()->equals($item, false, false)) {
                    foreach($tx->getBlocks() as [$x, $y, $z, $b]){
                        \AdvancedSpawner::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use($block, $b, $spawner) {
                            $potentialTile = $b->getPosition()->getWorld()->getTile($b->getPosition());
                            if($potentialTile instanceof Tile) {
                                $potentialTile->close();
                            }

                            $block->getPosition()->getWorld()->addTile(($tile = new MobSpawnerTile($block->getPosition()->getWorld(), $b->getPosition())));
                            $tile->setSpawnerType($spawner);
                        }), 20);
                    }
                    break;
                }
            }
        }, EventPriority::LOWEST, $this);
        $this->getServer()->getPluginManager()->registerEvent(BlockBreakEvent::class, function (BlockBreakEvent $event) {
            $block = $event->getBlock();
            $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());

            if($event->isCancelled()) {
                return;
            }

            if($tile instanceof MobSpawnerTile) {
                $event->setDrops([(clone $tile->getSpawnerType()->getBlock())->setCount($tile->getEgg())->setCustomName("§r§f{$tile->getSpawnerType()->getName()}")]);
                $tile->close();
            }
        }, EventPriority::LOWEST, $this);

        $this->getServer()->getPluginManager()->registerEvent(EntityDamageEvent::class, function (EntityDamageEvent $event) {
            $entity = $event->getEntity();
            if (!$entity instanceof MobEntity) return;

            $mobStacker = new Mobstacker($entity);
            if ($entity->getHealth() - $event->getFinalDamage() <= 0) {
                $cause = null;
                if($event instanceof EntityDamageByEntityEvent) {
                    $player = $event->getDamager();
                    if($player instanceof Player) $cause = $player;
                }
                if ($mobStacker->removeStack($cause)) $event->cancel();
            }
        }, EventPriority::LOWEST, $this);
        $this->getServer()->getPluginManager()->registerEvent(PlayerInteractEvent::class, function (PlayerInteractEvent $event): void
        {
            $player = $event->getPlayer();
            $block = $event->getBlock();
            $item = $player->getInventory()->getItemInHand();
            $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());

            $action = $event->getAction();
            if($action !== PlayerInteractEvent::LEFT_CLICK_BLOCK) return;

            if($tile instanceof MobSpawnerTile) {
                foreach (SpawnerHandler::getInstance()->getAll() as $spawner) {
                    if($spawner->getBlock()->equals($item, false, false)) {
                        $tile->addEgg();
                        $player->getInventory()->removeItem((clone $item)->setCount(1));
                        break;
                    }
                }
            }

        }, EventPriority::LOWEST, $this);
    }
}