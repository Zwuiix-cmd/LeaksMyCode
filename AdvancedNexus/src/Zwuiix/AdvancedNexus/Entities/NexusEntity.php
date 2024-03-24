<?php

namespace Zwuiix\AdvancedNexus\Entities;

use DaPigGuy\PiggyFactions\event\member\PowerChangeEvent;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\PlayerManager;
use JsonException;
use onebone\economyapi\EconomyAPI;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{ByteTag, CompoundTag};
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use Zwuiix\AdvancedNexus\Handler\Faction;
use Zwuiix\AdvancedNexus\Handler\NexusHandler;

class NexusEntity extends Entity
{
    public array $nexuscount = array();

    public function __construct(Location $location, protected Config $config, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public static function getNetworkTypeId(): string{ return EntityIds::ENDER_CRYSTAL; }

    protected function getInitialSizeInfo(): EntitySizeInfo{ return new EntitySizeInfo(1.8, 0.6, 1.62);}

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setNameTagAlwaysVisible();
        $this->setMaxHealth($this->config->getNested("entity.max-health"));
        $this->setHealth($this->config->getNested("entity.max-health"));
        $this->setScale($this->config->getNested("entity.scale"));
        parent::initEntity($nbt);
    }

    public function onUpdate(int $currentTick): bool
    {
        $heal = $this->getHealth() * 100 / $this->getMaxHealth();
        $this->setNameTag("§c{$heal}%");
        if($this->isOnFire()){
            $this->extinguish();
        }
        return parent::onUpdate($currentTick);
    }

    /**
     * @return array
     */
    public function getNexusCount(): array
    {
        return $this->nexuscount;
    }

    /**
     * @throws JsonException
     */
    public function attack(EntityDamageEvent $source) : void
    {
        if($this->isFlaggedForDespawn()) return;
        $source->cancel();
        $this->setHealth($this->getHealth()-1);
        $player = $source->getDamager();
        if(!$player instanceof Player)return;
        $faction=new Faction();
        if(!isset($this->nexuscount[$player->getName()])){
            $this->nexuscount[$player->getName()]=0;
            return;
        }
        $this->nexuscount[$player->getName()]=$this->nexuscount[$player->getName()]+1;

        $max=0;
        $playermax=null;
        foreach ($this->nexuscount as $players => $value){
            if($value > $max){
                $max=$value;
                $playermax=$players;
            }
        }
        $user=Server::getInstance()->getPlayerByPrefix($playermax);
        if(!$user instanceof Player)return;

        $point=$max-$this->nexuscount[$player->getName()];
        $player->sendTip(str_replace(["{POINT}", "{MAX}"], [$point, $max], $this->config->getNested("message.entity-info")));

        if($this->getHealth() <= 0){
            $this->flagForDespawn();
            $name = $user->getName();
            $faction_name=$faction->getPlayerFaction($user);

            Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{FACTION}"], [$name, $faction_name], $this->config->getNested("message.broadcast-winner")));

            NexusHandler::getInstance()->setNexus(false);

            $user->sendMessage($this->config->getNested("message.win"));
            if($this->config->getNested("win.power.activate")) {
                $member = PlayerManager::getInstance()->getPlayer($player);
                $member->setPower($member->getPower() + $this->config->getNested("win.power.count"));
            }
            if($this->config->getNested("win.money.activate")) EconomyAPI::getInstance()->addMoney($user, $this->config->getNested("win.money.count"));
            if($this->config->getNested("win.items.activate")) {
                $items=$this->config->getNested("win.items.items");
                foreach ($items as $item => $value){
                    $info=$value[$item];
                    $id=$info["id"]; $meta=$info["meta"]; $count=$info["count"];
                    $itemGiven=ItemFactory::getInstance()->get($id, $meta, $count);
                    if($user->getInventory()->canAddItem($itemGiven)){
                        $user->getInventory()->addItem($itemGiven);
                    }else{
                        $user->getWorld()->dropItem(new Vector3($this->getPosition()->getX(), $this->getPosition()->getY(), $this->getPosition()->getZ()), $itemGiven);
                    }
                }
            }

            if(NexusHandler::getInstance()->getBossBar() !== null){
                NexusHandler::getInstance()->getBossBar()->hideFromAll();
            }
        }
    }
}