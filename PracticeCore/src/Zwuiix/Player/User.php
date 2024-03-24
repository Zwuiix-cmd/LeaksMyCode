<?php

namespace Zwuiix\Player;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\sound\EntityAttackNoDamageSound;
use pocketmine\world\sound\EntityAttackSound;
use pocketmine\world\sound\ItemBreakSound;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Handler\Cooldown;
use Zwuiix\Main;
use Zwuiix\Player\trait\VariableTrait;
use Zwuiix\Utils\Utils;

class User extends Player
{
    use VariableTrait;

    public int|null|float $lastAttack = null;
    public int|float $lastAttackTime = 0;
    public int|null|float $lastSprint = null;
    public ?int $lastPlayerAuthInputFlags = null;

    protected const MAX_REACH_DISTANCE_ENTITY_INTERACTION = 5.7;

    public function hasLastDamagePosition(): bool
    {
        return $this->lastDamagePosition !== null;
    }

    public function getLastDamagePosition(): ?Position
    {
        return $this->lastDamagePosition;
    }

    public function setLastDamagePosition(Position $pos): void
    {
        $this->lastDamagePosition = $pos;
    }

    public function getAllCount(Item $item): int
    {
        $count=0;
        $items=$this->getInventory()->all($item);
        foreach ($items as $slot => $item) $count = $count + $item->getCount();
        return $count;
    }

    /**
     * @return float
     */
    public function getCps(): float
    {
        return AntiCheatHandler::getInstance()->getCPSHandler()->getCps($this);
    }

    /**
     * @return void
     */
    public function addCps(): void
    {
        AntiCheatHandler::getInstance()->getCPSHandler()->addClick($this);
    }

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $bXZ = 0.4;
        $bY = 0.4;

        if ($this->hasLastDamagePosition()) {
            $position = $this->getLastDamagePosition();
            if ($position instanceof Vector3) {
                $dist = $this->getPosition()->getY() - $position->getY();
                $addDist = $dist + 0.5;
                if (!$this->isOnGround()) {
                    $bool = $addDist > $this->maxDistanceKnockBack;
                    $diff = $bool ? 0.026 * 0.45 : 0.026;
                    $bY -= $dist * $diff;
                }
            }
        }

        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > AttributeFactory::getInstance()->mustGet(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motionX = $this->motion->x / 2;
            $motionY = $this->motion->y / 2;
            $motionZ = $this->motion->z / 2;
            $motionX += $x * $f * $bXZ;
            $motionY += $bY;
            $motionZ += $z * $f * $bXZ;

            //$verticalLimit ??= $bY;
            if ($motionY > $verticalLimit) {
                $motionY = $verticalLimit;
            }
            $this->setMotion(new Vector3($motionX, $motionY, $motionZ));
        }
    }

    /**
     * @return void
     */
    public function tick(): void
    {
        $this->currentTick++;
    }

    public function kit()
    {
        $this->setGamemode(GameMode::SURVIVAL());
        $this->getEffects()->clear();

        $inv=$this->getInventory();
        $armor=$this->getArmorInventory();

        $inv->clearAll();
        $armor->clearAll();
        $this->getCursorInventory()->clearAll();

        $armor->setHelmet($this->customItem(ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET), true, false));
        $armor->setChestplate($this->customItem(ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE), true, false));
        $armor->setLeggings($this->customItem(ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS), true, false));
        $armor->setBoots($this->customItem(ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS), true, false));

        $inv->setItem(0, $this->customItem(ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD), false, true));
        $inv->setItem(1, $this->customItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16)));

        $inv->addItem($this->customItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22, 99)));

        $this->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 9999999, 0, false));

        $this->setMaxHealth(20);
        $this->setHealth(20);
        $this->getXpManager()->setXpLevel(0);
        $this->getXpManager()->setXpProgress(0);
    }

    /**
     * @param Item $item
     * @param bool $protection
     * @param bool $sharpness
     * @return Durable|Item
     */
    public function customItem(Item $item, bool $protection = false, bool $sharpness = false): Durable|Item
    {

        $item->setCustomName("§r§9Practice");
        if(!$item instanceof Durable) return $item;
        $item->setUnbreakable();

        if($protection) $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
        if($sharpness) $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5));
        if($protection or $sharpness) $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));

        return $item;
    }

    public function spawn()
    {
        $this->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
        $this->kit();
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.kills", 0);
    }

    public function addKill(int $number = 1)
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.kills", $this->getKills() + $number);
    }

    /**
     * @return int
     */
    public function getDeaths(): int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.deaths", 0);
    }

    public function addDeath(int $number = 1)
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.deaths", $this->getDeaths() + $number);
    }

    /**
     * @return float|int
     */
    public function getKillStreak(): float|int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.killstreak", 0);
    }

    public function addKillStreak(int $number = 1)
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.killstreak", $this->getKillStreak() + $number);
    }

    public function resetKillStreak()
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.killstreak", 0);
    }

    /**
     * @return float|int
     */
    public function getRatio(): float|int
    {
        return $this->getDeaths() == 0 ? $this->getKills() : ($this->getKills() + $this->getKillStreak()) / $this->getDeaths();
    }

    public function setLastFight(User $damager)
    {
        $this->lastUserFight=$damager->getName();
        $this->lastUserFightContent=$damager->getInventory()->getContents();
        $this->lastThisUserFightContent=$this->getInventory()->getContents();

        $kP = Utils::getPotionsCount($damager);
        $tP = Utils::getPotionsCount($this);

        $result = abs($tP - $kP);

        $this->lastFightMessageKill="§a{$this->getName()}§2[{$tP}] §8- §a{$damager->getName()}§2[{$kP}]";
        $this->lastFightMessageDiff="§9{$result} potions de différence";
    }

    protected function onHitGround(): ?float
    {
        $fallBlockPos = $this->location->floor();
        $fallBlock = $this->getWorld()->getBlock($fallBlockPos);
        if (count($fallBlock->getCollisionBoxes()) === 0) {
            $fallBlockPos = $fallBlockPos->down();
            $fallBlock = $this->getWorld()->getBlock($fallBlockPos);
        }
        $newVerticalVelocity = $fallBlock->onEntityLand($this);

        $damage = $this->calculateFallDamage($this->fallDistance);
        if ($damage > 0) {
            $ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FALL, $damage);
            $this->attack($ev);
        }

        return $newVerticalVelocity;
    }

    public function isOp(): bool
    {
        return Server::getInstance()->isOp($this->getName());
    }

    public function getCooldown() : Cooldown{
        return new Cooldown($this);
    }

    /**
     * Attacks the given entity with the currently-held item.
     * TODO: move this up the class hierarchy
     *
     * @return bool if the entity was dealt damage
     */
    public function attackEntity(Entity $entity) : bool
    {
        if(!$entity->isAlive()){
            return false;
        }
        if($entity instanceof ItemEntity || $entity instanceof Arrow){
            $this->logger->debug("Attempted to attack non-attackable entity " . get_class($entity));
            return false;
        }

        $heldItem = $this->inventory->getItemInHand();
        $oldItem = clone $heldItem;

        $ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $heldItem->getAttackPoints());
        if(!$this->canInteract($entity->getLocation(), self::MAX_REACH_DISTANCE_ENTITY_INTERACTION)){
            $this->logger->debug("Cancelled attack of entity " . $entity->getId() . " due to not currently being interactable");
            $ev->cancel();
        }elseif($this->isSpectator() || ($entity instanceof Player && !$this->server->getConfigGroup()->getConfigBool("pvp"))){
            $ev->cancel();
        }

        $meleeEnchantmentDamage = 0;
        /** @var EnchantmentInstance[] $meleeEnchantments */
        $meleeEnchantments = [];
        foreach($heldItem->getEnchantments() as $enchantment){
            $type = $enchantment->getType();
            if($type instanceof MeleeWeaponEnchantment && $type->isApplicableTo($entity)){
                $meleeEnchantmentDamage += $type->getDamageBonus($enchantment->getLevel());
                $meleeEnchantments[] = $enchantment;
            }
        }
        $ev->setModifier($meleeEnchantmentDamage, EntityDamageEvent::MODIFIER_WEAPON_ENCHANTMENTS);

        $entity->attack($ev);
        $this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());

        $soundPos = $entity->getPosition()->add(0, $entity->size->getHeight() / 2, 0);
        if($ev->isCancelled()){
            $this->getWorld()->addSound($soundPos, new EntityAttackNoDamageSound());
            return false;
        }
        $this->getWorld()->addSound($soundPos, new EntityAttackSound());

        foreach($meleeEnchantments as $enchantment){
            $type = $enchantment->getType();
            assert($type instanceof MeleeWeaponEnchantment);
            $type->onPostAttack($this, $entity, $enchantment->getLevel());
        }

        if($this->isAlive()){
            //reactive damage like thorns might cause us to be killed by attacking another mob, which
            //would mean we'd already have dropped the inventory by the time we reached here
            if($heldItem->onAttackEntity($entity) && $this->hasFiniteResources() && $oldItem->equalsExact($this->inventory->getItemInHand())){ //always fire the hook, even if we are survival
                if($heldItem instanceof Durable && $heldItem->isBroken()){
                    $this->broadcastSound(new ItemBreakSound());
                }
                $this->inventory->setItemInHand($heldItem);
            }
        }

        return true;
    }

    /**
     * @param int $number
     * @return void
     */
    public function addAllHit(int $number = 1): void
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.allHits", $this->getAllHit() + $number);
    }

    /**
     * @return int
     */
    public function getAllHit(): int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.allHits", 0);
    }

    public function addWTAPHit(int $number = 1): void
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.WTAPHits", $this->getWTAPHit() + $number);
    }

    /**
     * @return int
     */
    public function getWTAPHit(): int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.WTAPHits", 0);
    }

    /**
     * @return int
     */
    public function getWTAPPercent(): int
    {
        $totalAll=$this->getAllHit();
        $totalAllwTap=$this->getWTAPHit();

        return $this->getPercent($totalAll, $totalAllwTap);
    }

    /**
     * @param int $v1
     * @param int $v2
     * @return float|int
     */
    public function getPercent(int $v1, int $v2): float|int
    {
        return $v1 === 0 ? 0 : ($v2 === 0 ? 0 : $v2 * 100 / $v1);
    }

    /**
     * @param int $cps
     * @return void
     */
    public function addGlobalCPS(int $cps): void
    {
        $global=$this->getGlobalCPS();
        $global[]=$cps;
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.globalcps", $global);
    }

    /**
     * @return array
     */
    public function getGlobalCPS(): array
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.globalcps", []);
    }

    public function setFirstPlayed()
    {
        Main::getInstance()->playersdata->setNested("{$this->getXuid()}.firstplayed", time());
    }

    public function getFirstPlayed(): ?int
    {
        return Main::getInstance()->playersdata->getNested("{$this->getXuid()}.firstplayed", time());
    }

    /**
     * @param bool $verif
     * @return float
     */
    public function getMoyenneCPS(): float
    {
        $result=$this->getGlobalCPS();

        $initialize = 0;
        for ($i = 0; $i < count($result); $i ++) $initialize = $initialize + $result[$i];
        return $initialize === 0 ? 0 : (count($result) === 0 ? 0 : round($initialize / count($result)));
    }
}