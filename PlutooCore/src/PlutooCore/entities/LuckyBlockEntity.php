<?php

namespace PlutooCore\entities;

use MusuiEssentials\MusuiPlayer;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\particle\HugeExplodeParticle;

class LuckyBlockEntity extends Entity
{
    public int|float $time = 20 * 2;
    protected ?MusuiPlayer $player = null;
    public static function getNetworkTypeId() : string{ return "plutoonium:lucky_block"; }
    public function __construct(Location $location, ?CompoundTag $nbt = null){
        parent::__construct($location, $nbt);
        $this->setNoClientPredictions(true);
    }

    /**
     * @param MusuiPlayer $player
     * @return LuckyBlockEntity
     */
    public function setPlayer(MusuiPlayer $player): LuckyBlockEntity
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick) : bool {
        $this->time--;
        if($this->time <= 0) {
            $this->open();
        } else {
            $currentLoc = $this->getLocation();
            $this->setRotation($currentLoc->getYaw() + 5.5, $currentLoc->getPitch());
            $this->move($this->motion->x, $this->motion->y + 0.05, $this->motion->z);
            $this->updateMovement();
        }
        return parent::onUpdate($currentTick);
    }

    public function open(): void
    {
        $this->kill();
        for ($i = 0; $i < 3; $i++) {
            $motion = new Vector3(mt_rand() / mt_getrandmax() - 0.5, mt_rand() / mt_getrandmax() - 0.5, mt_rand() / mt_getrandmax() - 0.5);
            $this->getWorld()->addParticle($this->getPosition()->addVector($motion->normalize()->multiply(mt_rand(0, 5))), new HugeExplodeParticle());
        }

        if(is_null($this->player)) return;
        if(!$this->player->isConnected()) return;
        $player = $this->player;

        $rand = mt_rand(1, 600) / 100;
        if($rand <= 0.03) {
            $player->addMoney(50000);
            $player->sendMessage("§5Vous avez reçu §950000$ §5!");
        } elseif($rand <= 0.17) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaItems::EMERALD());
            $player->sendMessage("§5C'est noel ou quoi ?");
        } elseif($rand <= 0.23) {
            $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 15, 3, false));
            $player->sendMessage("§5Fatigué ?");
        } elseif($rand <= 0.26) {
            $player->setHealth(mt_rand(1, 2));
            foreach ($player->getArmorInventory()->getContents() as $key => $item) {
                if($item instanceof Armor) {
                    $item->setDamage($item->getMaxDurability() - 1);
                    $player->getArmorInventory()->setItem($key, $item);
                }
            }
            $player->sendMessage("§5Frôler la mort?");
        } elseif($rand <= 0.57) {
            $this->getWorld()->dropItem($this->getPosition(), StringToItemParser::getInstance()->parse("turtle_shell_piece"));
            $this->getWorld()->dropItem($this->getPosition(), StringToItemParser::getInstance()->parse("sugar"));
            $this->getWorld()->dropItem($this->getPosition(), StringToItemParser::getInstance()->parse("ghast_tear"));
            $player->sendMessage("§5Le batton sans la carotte");
        } elseif($rand <= 0.97) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaItems::IRON_NUGGET());
            $player->sendMessage("§5C'est cadeau");
        } elseif($rand <= 1.2) {
            $player->getWorld()->setBlock($player->getPosition()->add(0, 2, 0), VanillaBlocks::ANVIL());
            $player->sendMessage("§5Attention la tête");
        } elseif($rand <= 1.44) {
            $array = [VanillaItems::LEATHER_CAP(), VanillaItems::LEATHER_TUNIC(), VanillaItems::LEATHER_PANTS(), VanillaItems::LEATHER_BOOTS()];
            $randItem = $array[array_rand($array)];
            $this->getWorld()->dropItem($this->getPosition(), $randItem);
            $player->sendMessage("§5On farm ?");
        } elseif($rand <= 1.60) {
            $player->getWorld()->setBlock($player->getPosition()->add(0, 2, 0), VanillaBlocks::WATER());
            $player->sendMessage("§5Garde la tête froide");
        } elseif($rand <= 1.94) {
            $player->getEffects()->add(new EffectInstance(VanillaEffects::LEVITATION(), 20 * 30, 3, false));
            $player->sendMessage("§5Comme un petit oiseau");
        } elseif($rand <= 2.27) {
            $player->getWorld()->setBlock($player->getPosition()->add(0, $player->getEyeHeight(), 0), VanillaBlocks::COBWEB());
            $player->sendMessage("§5Trop relou");
        } elseif($rand <= 3.27) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaItems::DIAMOND()->setCount(5));
            $player->sendMessage("§5Le début de la richesse");
        } elseif($rand <= 3.51) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaItems::NETHER_STAR());
            $player->sendMessage("§5Ca plane pour moi");
        } elseif($rand <= 4.17) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaItems::SUGAR()->setCount(1));
            $player->sendMessage("§5La vitesse ? bien ça");
        } elseif($rand <= 4.47) {
            $player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 20 * 30, false));
            $player->sendMessage("§5Lendemain compliqué");
        } elseif($rand <= 5) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaBlocks::OBSIDIAN()->asItem()->setCount(64 * 3));
            $player->sendMessage("§5Une base claim ?");
        } elseif($rand <= 5.34) {
            $player->addMoney(2000);
            $player->sendMessage("§5Un petit salaire");
        } elseif($rand <= 5.5) {
            $array = [VanillaItems::GOLDEN_HELMET(), VanillaItems::GOLDEN_CHESTPLATE(), VanillaItems::GOLDEN_LEGGINGS(), VanillaItems::GOLDEN_BOOTS()];
            $randItem = $array[array_rand($array)];
            if(!$randItem instanceof Item) return;
            $randItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4));
            $randItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3));
            $this->getWorld()->dropItem($this->getPosition(), $randItem);
            $player->sendMessage("§5Bon pour le combat!");
        } elseif($rand <= 6) {
            $this->getWorld()->dropItem($this->getPosition(), VanillaBlocks::DIAMOND()->asItem()->setCount(64));
            $player->sendMessage("§5Bling bling");
        }
    }

    public function getInitialSizeInfo(): EntitySizeInfo {return new EntitySizeInfo(0, 0);}
    public function getInitialDragMultiplier(): float {return 0;}
    public function getInitialGravity(): float {return 0;}
}