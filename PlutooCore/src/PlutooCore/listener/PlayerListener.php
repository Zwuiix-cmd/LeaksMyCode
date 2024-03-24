<?php

namespace PlutooCore\listener;

use Error;
use JsonException;
use MusuiEssentials\handlers\protection\Area;
use MusuiEssentials\handlers\protection\ProtectionHandler;
use MusuiEssentials\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;
use MusuiEssentials\managers\CooldownManager;
use MusuiEssentials\MusuiPlayer;
use MusuiEssentials\utils\Cooldown;
use MusuiEssentials\utils\ReflectionUtils;
use PlutooCore\block\tile\CrateTile;
use PlutooCore\entities\Balloon;
use PlutooCore\entities\LuckyBlockEntity;
use PlutooCore\handlers\event\Event;
use PlutooCore\handlers\event\EventHandler;
use PlutooCore\handlers\OptionsHandler;
use PlutooCore\handlers\ShopHandler;
use PlutooCore\interface\EnchantForm;
use PlutooCore\interface\ShopUI;
use PlutooCore\item\CustomSword;
use PlutooCore\item\InfiniteSnowball;
use PlutooCore\player\CustomMusuiPlayer;
use PlutooCore\task\ImmobileTask;
use PlutooCore\task\KothTask;
use pocketmine\block\Anvil;
use pocketmine\block\BaseSign;
use pocketmine\block\Beetroot;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Chest;
use pocketmine\block\CraftingTable;
use pocketmine\block\EnchantingTable;
use pocketmine\block\EnderChest;
use pocketmine\block\NetherWartPlant;
use pocketmine\block\Sponge;
use pocketmine\block\tile\Tile;
use pocketmine\block\utils\SignText;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldSaveEvent;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\pocketkitmap\event\ServerBreakBlockEvent;
use pocketmine\Server;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\sound\AnvilFallSound;
use ReflectionException;

class PlayerListener
{
    protected array $combatStick = [];

    /**
     * @param PlayerCreationEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function onCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(CustomMusuiPlayer::class);
        $event->setBaseClass(MusuiPlayer::class);
    }

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof MusuiPlayer) return;
        if($player->getEffects()->has(VanillaEffects::LEVITATION())) {
            $player->getEffects()->remove(VanillaEffects::LEVITATION());
        }
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if(!$player instanceof MusuiPlayer) return;
        $cause = $event->getCause();

        if($cause === EntityDamageEvent::CAUSE_FALL) {
            $hand = $player->getInventory()->getItemInHand();
            if($hand->equals(VanillaItems::NETHER_STAR(), false, false) || $hand->equals(VanillaItems::FLINT_AND_STEEL(), false, false)) {
                $event->cancel();
            }
            return;
        }

        if($event->isCancelled() || $player->hasNoClientPredictions()) return;
        if($event instanceof EntityDamageByEntityEvent) {

            $damager = $event->getDamager();
            if(!$damager instanceof MusuiPlayer) return;

            $this->combatStick[strtolower($damager->getName())] = ["player" => strtolower($player->getName()), "time" => time() + 10];
            if($cause === EntityDamageEvent::CAUSE_PROJECTILE) {
                if($player->getEffects()->has(VanillaEffects::WATER_BREATHING())) {
                    $event->setKnockBack(0);
                    $event->cancel();
                    $player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_CUSTOM, 1));
                }
                return;
            }

            $damagerItem = $damager->getInventory()->getItemInHand();
            if($damagerItem->equals(VanillaItems::GHAST_TEAR(), false, false)) {
                $cooldown = CooldownManager::getInstance()->get($damager, "Sceptre royal");
                if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
                    $damager->sendActionBarMessage(Cooldown::inCooldownResponse($cooldown));
                    return;
                }

                $durability = $damagerItem->getNamedTag()->getInt("durability", 10);
                $damagerItem->getNamedTag()->setInt("durability", ($newDura = $durability - 1));
                $damagerItem->setLore(["§r§7Durabilité: {$newDura}/10"]);
                if($newDura <= 0) $damagerItem->pop();

                $damager->getInventory()->setItemInHand($damagerItem);
                new ImmobileTask($player);

                CooldownManager::getInstance()->setCooldown($damager, "Sceptre royal", 60);
            }

            if($damagerItem instanceof CustomSword) {
                $celeste = VanillaItems::NETHERITE_SWORD();
                $pluto = VanillaItems::STONE_SWORD();
                $opa = VanillaItems::GOLDEN_SWORD();

                if(!$damagerItem->equals($opa, false, false) && $damagerItem->equals($pluto, false, false) && $damagerItem->equals($celeste, false, false)) return;

                $num = rand(1, 100);
                if($num <= 4) {
                    $pos = $player->getPosition();

                    if($celeste->equals($damagerItem, false, false)) {
                        foreach ($pos->getWorld()->getViewersForPosition($pos) as $value) {
                            if($value instanceof MusuiPlayer && $value->isConnected()) {
                                if(!OptionsHandler::getInstance()->get($value->getName(), "lightning", true)) continue;
                                $value->getNetworkSession()->sendDataPacket(AddActorPacket::create(
                                    ($id = Entity::nextRuntimeId()), $id,
                                    "minecraft:lightning_bolt", $pos->asVector3(), null, 0.0, 0.0, 0.0, 0.0,
                                    [], [], new PropertySyncData([], []), []));
                                $value->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1));
                            }
                        }
                    }
                    $minX = $pos->x - 1;
                    $maxX = $pos->x + 1;
                    $minZ = $pos->z - 1;
                    $maxZ = $pos->z + 1;

                    for ($x = $minX; $x <= $maxX; $x++) {
                        for ($z = $minZ; $z <= $maxZ; $z++) $pos->getWorld()->addParticle(new Vector3($x,$pos->y + $player->getEyeHeight(),$z), new FlameParticle());
                    }
                    $player->getWorld()->addSound($pos->asVector3(), new AnvilFallSound());
                    $pos->getWorld()->broadcastPacketToViewers($pos, PlaySoundPacket::create("random.anvil_land", $pos->getX(), $pos->getY(), $pos->getZ(), 80, 1));
                    $event->setVerticalKnockBackLimit(1);
                    $event->setKnockBack(1);
                }
            }
        }
    }

    /**
     * @param PlayerItemUseEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof MusuiPlayer) return;

        $item = $player->getInventory()->getItemInHand();
        if(!$player->hasNoClientPredictions()) {
            if($item->equals(VanillaItems::SHEARS(), false, false)) {
                $cooldown = CooldownManager::getInstance()->get($player, "Heal Stick");
                if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
                    $player->sendActionBarMessage(Cooldown::inCooldownResponse($cooldown));
                    return;
                }

                if(floor($player->getHealth()) <= floor($player->getMaxHealth())) {
                    $player->setHealth($player->getHealth() + 10);
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    $player->sendActionBarMessage("§a+ 10");
                    CooldownManager::getInstance()->setCooldown($player, "Heal Stick", 10);
                }
            }
        }

        if($item->equals(VanillaItems::SCUTE(), false, false)) {
            $cooldown = CooldownManager::getInstance()->get($player, "Stick de combat");
            if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
                $player->sendActionBarMessage(Cooldown::inCooldownResponse($cooldown));
                return;
            }

            if(!isset($this->combatStick[strtolower($player->getName())])) {
                return;
            }
            $lastAttack = $this->combatStick[strtolower($player->getName())];
            if(!isset($lastAttack["player"]) || !isset($lastAttack["time"])) return;

            $target = Server::getInstance()->getPlayerExact($lastAttack["player"]);
            if(!$target instanceof MusuiPlayer || !$target->isConnected()) return;

            $attackTime = new Cooldown("AttackTime", $lastAttack["time"]);
            if(!$attackTime->isInCooldown()) return;

            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $player->teleport($target->getPosition());
            CooldownManager::getInstance()->setCooldown($player, "Stick de combat", 30);
        }

        if($item->equals(VanillaItems::SUGAR(), false, false)) {
            $cooldown = CooldownManager::getInstance()->get($player, "Bâton de vitesse");
            if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
                $player->sendActionBarMessage(Cooldown::inCooldownResponse($cooldown));
                return;
            }

            $durability = $item->getNamedTag()->getInt("durability", 10);
            $item->getNamedTag()->setInt("durability", ($newDura = $durability - 1));
            $item->setLore(["§r§7Durabilité: {$newDura}/10"]);
            if($newDura <= 0) $item->pop();
            $player->getInventory()->setItemInHand($item);
            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 10, 2, false));
            CooldownManager::getInstance()->setCooldown($player, "Bâton de vitesse", 40);
        }
    }

    /**
     * @param PlayerItemHeldEvent $event
     * @return void
     * @throws ReflectionException
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function onItemHeld(PlayerItemHeldEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof MusuiPlayer) return;
        $hand = $event->getItem();
        $found = false;
        if($hand->equals(VanillaItems::NETHER_STAR(), false, false)) {
            $effectInstance = new \PlutooCore\utils\EffectInstance(VanillaEffects::LEVITATION(), 99999999, -3, false);
            $player->getEffects()->add($effectInstance);
            $found = true;
        }

        if(isset(Balloon::$entity[strtolower($player->getName())])) {
            Balloon::$entity[strtolower($player->getName())]->kill();
            unset(Balloon::$entity[strtolower($player->getName())]);
        }
        if($hand->equals(VanillaItems::FLINT_AND_STEEL(), false, false)) {
            $entity = new Balloon($player->getLocation(), musuiPlayer: $player);
            $entity->spawnToAll();
            Balloon::$entity[strtolower($player->getName())] = $entity;

            $effectInstance = new \PlutooCore\utils\EffectInstance(VanillaEffects::LEVITATION(), 99999999, -3, false);
            $player->getEffects()->add($effectInstance);
            $found = true;
        }

        if(!$found && $player->getEffects()->has(VanillaEffects::LEVITATION())) $player->getEffects()->remove(VanillaEffects::LEVITATION());
    }

    /**
     * @param PlayerMoveEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGH)]
    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof MusuiPlayer) {
            throw new Error("Invalid Session");
        }

        if($player->isCreative()) return;

        $koth = EventHandler::getInstance()->getEventWithName("Koth");
        if($koth instanceof Event && $koth->isStarted()) {
            if(($kothTask = KothTask::getInstance())->area->isInArea($player->getPosition())) {
                $kothTask->syncUser($player);
                return;
            }
            $kothTask->removeUser($player);
        }

        if($player->getLocation()->getFloorY() <= 0 && $player->isAlive()) {
            $player->teleport($player->getWorld()->getSafeSpawn());
        }

        $hand = $player->getInventory()->getItemInHand();
        if ($player->getEffects()->has(VanillaEffects::WATER_BREATHING())) {
            $effect = $player->getEffects()->get(VanillaEffects::WATER_BREATHING());
            $player->sendActionBarMessage("§cEffet AntiBdnKb: §e" . (new Cooldown("antibdncooldown", (time() + (int)round($effect->getDuration() / 20))))->toString());
        }
    }

    /**
     * @param WorldSaveEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function onWorldSave(WorldSaveEvent $event): void
    {
        $world = $event->getWorld();
        foreach ($world->getEntities() as $entity) {
            if($entity instanceof Human) continue;

            if($entity instanceof ItemEntity or $entity instanceof ExperienceOrb){
                $entity->flagForDespawn();
            }
            if($entity instanceof Living && $entity->getNameTag() === "") {
                $entity->flagForDespawn();
            }
        }
    }

    /**
     * @param WorldLoadEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function onWorldLoad(WorldLoadEvent $event): void
    {
        $world = $event->getWorld();
        foreach ($world->getEntities() as $entity) {
            if($entity instanceof Human) continue;
            if($entity instanceof ItemEntity or $entity instanceof ExperienceOrb){
                $entity->flagForDespawn();
            }
            if($entity instanceof Living && $entity->getNameTag() === "") {
                $entity->flagForDespawn();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof CustomMusuiPlayer) return;
        $block = $event->getBlock();
        $action = $event->getAction();
        $hand = $player->getInventory()->getItemInHand();
        $world = $block->getPosition()->getWorld();

        $tile = $world->getTile($block->getPosition());
        if(!$player->isCreative() && $tile instanceof CrateTile) {
            if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
                $tile->openCrate($player);
            }elseif ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
                $tile->openPreview($player);
            }
            $event->cancel();
            return;
        }
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $player->isInCreateCrate() && $player->getCreateCrate() !== null){
            $info = $player->getCreateCrate();
            if($tile instanceof Tile) $tile->close();
            $newTile = new CrateTile($world, $block->getPosition());
            $newTile->setCrateName($info->getName());
            $newTile->setCrate($info);
            $world->addTile($newTile);
            $player->sendMessage("§aVous avez bien crée une caisse §9{$info->getName()}§a!");
            $player->setCreateCrate(false);
            $event->cancel();
            return;
        }
        
        if($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if($block instanceof Sponge && $block->getPosition()->getWorld()->getFolderName() !== Server::getInstance()->getWorldManager()->getDefaultWorld()->getFolderName()) {
                $block->getPosition()->getWorld()->setBlock($block->getPosition()->asVector3(), VanillaBlocks::AIR());
                (new LuckyBlockEntity(Location::fromObject($block->getPosition()->add(0.5, 0, 0.5), $block->getPosition()->getWorld())))->setPlayer($player)->spawnToAll();
                $event->cancel();
            }
            if($block instanceof BaseSign) {
                $lines = $block->getText()->getLines();
                if(count($lines) >= 4) {
                    $dataWithPos = ShopHandler::getInstance()->get($block->getPosition()->asVector3());
                    if($dataWithPos === []) return;

                    $linesData = $dataWithPos;
                    $type = intval($linesData[0]);
                    $item = StringToItemParser::getInstance()->parse($linesData[1]);
                    if(!$item instanceof Item || $item->isNull()) return;
                    $count = intval($linesData[2]);
                    if($count === 0) return;

                    $price = intval($linesData[3]);
                    if($price === 0) return;

                    $name = $item->getVanillaName();
                    if($item instanceof Potion) $name = $item->getType()->getDisplayName();

                    $result = ShopHandler::getInstance()->format()($type, $name, $count, $price);
                    if($result !== $lines) return;
                    $name = $dataWithPos[1];
                    ShopUI::send($player, $type, $name, $count, $price);
                    $event->cancel();
                }
            }

            if($block instanceof EnchantingTable) {
                $event->cancel();
                EnchantForm::getInstance()->send($player);
            }
            if($block instanceof Anvil) {
                $event->cancel();

                if(!$player->hasMoney(250)) {
                    $player->sendMessage("§cVous n'avez pas assez d'argent.");
                    return;
                }

                $find = false;
                if($hand->equals(VanillaItems::SUGAR(), false, false) || $hand->equals(VanillaItems::GHAST_TEAR(), false, false)) {
                    $durability = $hand->getNamedTag()->getInt("durability", 10);
                    if($durability >= 10) {
                        $player->sendMessage("§cVous ne pouvez pas réparé cet item.");
                        return;
                    }
                    $find = true;
                    $hand->getNamedTag()->setInt("durability", 10);
                }
                if($hand instanceof InfiniteSnowball) {
                    $durability = $hand->getNamedTag()->getInt("durability", 200);
                    if($durability >= 200) {
                        $player->sendMessage("§cVous ne pouvez pas réparé cet item.");
                        return;
                    }
                    $find = true;
                    $hand->getNamedTag()->setInt("durability", 10);
                }
                if(!$find && !$hand instanceof Durable) {
                    $player->sendMessage("§cVous ne pouvez pas réparé cet item.");
                    return;
                } elseif($hand instanceof Durable) {
                    if($hand->getDamage() === 0) {
                        $player->sendMessage("§cVous ne pouvez pas réparé cet item, il est déjà réparé.");
                        return;
                    }
                    $hand->setDamage(0);
                    $hand->getNamedTag()->setInt("durability", $hand->getMaxDurability());
                }

                $player->sendMessage("§5Vous avez bien réparé votre item pour §9250$ §5!");
                $pos = $block->getPosition();
                $pos->getWorld()->broadcastPacketToViewers($pos, PlaySoundPacket::create("random.anvil_use", $pos->getX(), $pos->getY(), $pos->getZ(), 80, 1));
                $player->reduceMoney(250);
                $player->getInventory()->setItemInHand($hand);
                return;
            }
            if($block instanceof EnderChest || $block instanceof CraftingTable || $block instanceof Chest) {
                $area = ProtectionHandler::getInstance()->findAreaWithPosition($block->getPosition());
                if($area instanceof Area && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    $event->uncancel();
                }
            }
        }
    }

    /**
     * @param ServerBreakBlockEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function serverBreak(ServerBreakBlockEvent $event): void
    {
        $block = $event->getBlock();
        if($event->isCancelled()) return;
        if($block instanceof Beetroot) {
            $event->setDrops([VanillaItems::BEETROOT_SEEDS()]);
        }
        if($block instanceof NetherWartPlant) {
            $event->setDrops([VanillaBlocks::NETHER_WART()->asItem()]);
        }
    }

    /**
     * @param BlockBreakEvent $event
     * @return void
     * @throws JsonException
     */
    #[EventAttribute(EventPriority::HIGHEST)]
    public function blockBreak(BlockBreakEvent $event): void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if($event->isCancelled()) return;
        if($block instanceof BaseSign && $player->isCreative()) {
            $pos = $block->getPosition()->asVector3()->floor();
            if(ShopHandler::getInstance()->get($pos) !== []) {
                ShopHandler::getInstance()->remove($pos);
            }
        }

        if($block instanceof Beetroot) {
            if($block->getAge() >= $block::MAX_AGE){
                $rand = mt_rand(1, 100);
                if($rand <= 2) {
                    $rand2 = mt_rand(1, 100);
                    if($rand2 <= 1) {
                        $event->setDrops([VanillaItems::EMERALD()]);
                    } else $event->setDrops([VanillaItems::IRON_NUGGET()]);
                    return;
                }

                $rand2 = mt_rand(1, 10);
                if($rand2 <= 1) {
                    $event->setDrops([VanillaItems::BEETROOT_SEEDS()]);
                    return;
                }

                $event->setDrops([]);
                return;
            }
            $event->setDrops([VanillaItems::BEETROOT_SEEDS()]);
        }

        if($block instanceof NetherWartPlant) {
            if($block->getAge() >= $block::MAX_AGE){
                $rand = mt_rand(1, 50);
                if($rand <= 1) {
                    $event->setDrops([VanillaItems::GOLD_INGOT()]);
                    return;
                }

                $rand2 = mt_rand(1, 8);
                if($rand2 <= 1) {
                    $event->setDrops([VanillaBlocks::NETHER_WART()->asItem()]);
                    return;
                }

                $event->setDrops([]);
                return;
            }
            $event->setDrops([VanillaBlocks::NETHER_WART()->asItem()]);
        }

        if($block->getTypeId() === BlockTypeIds::GOLD_ORE) {
            $event->setDrops([VanillaItems::GOLD_INGOT()]);
        } elseif($block->getTypeId() === BlockTypeIds::IRON_ORE) {
            $rand = mt_rand(1, 150);
            if($rand <= 1) {
                $event->setDrops([VanillaItems::IRON_NUGGET()]);
            } elseif($rand <= 100) {
                $event->setDrops([VanillaItems::BEETROOT_SEEDS()]);
            } else $event->setDrops([]);
        }
        if($block->getTypeId() === BlockTypeIds::LAPIS_LAZULI_ORE) {
            $rand = mt_rand(1, 110);
            if($rand <= 1) {
                $event->setDrops([VanillaItems::IRON_NUGGET()]);
            } elseif($rand <= 25) {
                $event->setDrops([VanillaBlocks::SPONGE()->asItem()]);
            } elseif($rand <= 35) {
                $event->setDrops([VanillaItems::GOLD_INGOT()]);
            } elseif($rand <= 60) {
                $event->setDrops([VanillaItems::DIAMOND()]);
            } elseif($rand <= 85) {
                $event->setDrops([VanillaItems::COAL()]);
            } elseif($rand <= 110) {
                $event->setDrops([VanillaItems::REDSTONE_DUST()]);
            }
        }
    }

    /**
     * @param SignChangeEvent $event
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    #[EventAttribute(EventPriority::LOWEST)]
    public function signChange(SignChangeEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof CustomMusuiPlayer) return;
        $lines = $event->getNewText()->getLines();
        if(count($lines) >= 4) {
            if(!(intval($lines[0]) == ShopHandler::TYPE_BUY || intval($lines[0]) == ShopHandler::TYPE_SELL)) return;
            $type = intval($lines[0]);
            $item = StringToItemParser::getInstance()->parse($lines[1]);
            if(!$item instanceof Item || $item->isNull()) return;
            $count = intval($lines[2]);
            if($count === 0) return;

            $price = intval($lines[3]);
            if($price === 0) return;

            if(!Server::getInstance()->isOp($player->getRealName())) return;
            $name = $item->getVanillaName();
            if($item instanceof Potion) $name = $item->getType()->getDisplayName();
            ReflectionUtils::setProperty(SignText::class, $event->getNewText(), "lines", ShopHandler::getInstance()->format()($type, $name, $count, $price));
            ShopHandler::getInstance()->add($event->getSign()->getPosition()->asVector3(), $lines);
        }
    }
}