<?php

namespace PlutooCore\block\tile;

use MusuiEssentials\libs\muqsit\invmenu\InvMenu;
use MusuiEssentials\libs\muqsit\invmenu\type\InvMenuTypeIds;
use PlutooCore\entities\FloatingTextEntity;
use PlutooCore\handlers\crate\Crate;
use PlutooCore\handlers\crate\CrateHandler;
use PlutooCore\handlers\crate\CrateItem;
use PlutooCore\player\CustomMusuiPlayer;
use PlutooCore\task\CrateRouletteTask;
use pocketmine\block\tile\Chest;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\world\World;

class CrateTile extends Chest
{
    protected string $name = "";
    protected bool $isOpened = false;
    protected ?Crate $crate = null;
    protected ?FloatingTextEntity $entity = null;

    /**
     * @param World $world
     * @param Vector3 $pos
     */
    public function __construct(
        protected World $world,
        protected Vector3 $pos,
    ) {
        parent::__construct($world, $pos);
    }

    public function getCrateName(): string
    {
        return $this->name;
    }

    public function setCrateName(string $str): void
    {
        $this->name = $str;
    }

    /**
     * @return Crate|null
     */
    public function getCrate(): ?Crate
    {
        return $this->crate ?? CrateHandler::getInstance()->getCrateByName($this->getCrateName());
    }

    public function setCrate(Crate $crate): void
    {
        $this->crate = $crate;
    }

    /**
     * @return CrateItem[]
     */
    public function getLoots(): array
    {
        return $this->getCrate()->getLoots();
    }

    public function readSaveData(CompoundTag $nbt): void
    {
        parent::readSaveData($nbt);
        $this->name = $nbt->getString("crateName", "Inconnue");

        $crate = $this->getCrate();
        if($crate instanceof Crate) {
            $this->entity = new FloatingTextEntity(new Location($this->pos->getX() + 0.5, $this->pos->getY() + 1.2, $this->pos->getZ() + 0.5, $this->world, 0, 0));
            $this->entity->setNameTag("Caisse §5{$crate->getName()}");
            $this->entity->spawnToAll();
        }
    }

    public function writeSaveData(CompoundTag $nbt): void
    {
        parent::writeSaveData($nbt);
        $nbt->setString("crateName", $this->name);
    }

    public function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        parent::addAdditionalSpawnData($nbt);
        $nbt->setString(self::TAG_ID, "Chest");
    }

    /**
     * @param CustomMusuiPlayer $session
     * @return void
     */
    public function openPreview(CustomMusuiPlayer $session): void
    {
        if($this->getCrate() === null) return;
        $menu = InvMenu::create(count($this->getCrate()->getLoots()) > 27 ? InvMenuTypeIds::TYPE_DOUBLE_CHEST : InvMenuTypeIds::TYPE_CHEST);
        $menu->setName("Caisse §5{$this->getCrateName()}");
        $menu->setListener(InvMenu::readonly());

        $drops = $this->getLoots();
        usort($drops, function (CrateItem $a, CrateItem $b) {
            if ($a->getChance() > $b->getChance()) return -1;
            if ($a->getChance() < $b->getChance()) return 1;
            return 0;
        });

        $chances = 0;
        foreach ($drops as $crateItem) $chances += $crateItem->getChance();

        $inv=$menu->getInventory();
        foreach ($this->getLoots() as $crateItem){
            $chance=round(($crateItem->getChance() / $chances) * 100, 2);
            $inv->addItem($crateItem->getItem()->setLore(["§r§7Chance: {$chance}%"]));
        }

        $menu->send($session);
    }

    /**
     * @param CustomMusuiPlayer $session
     * @return void
     */
    public function openCrate(CustomMusuiPlayer $session): void
    {
        $inv=$session->getInventory();
        $item=$inv->getItemInHand();

        if(!$this->isValidKey($item)){
            $session->sendMessage("§cVous devez avoir une clé §5{$this->name}§c pour ouvrir cette caisse!");
            return;
        }
        if($this->isOpened){
            $session->sendMessage("§cLa caisse et déjà ouverte par un autre joueur, veuillez patientez...");
            return;
        }

        $this->isOpened = true;

        $item->pop();
        $inv->setItemInHand($item);
        $this->getPosition()->getWorld()->broadcastPacketToViewers($this->getPosition(), BlockEventPacket::create(BlockPosition::fromVector3($this->getPosition()->asVector3()), 1, 1));
        \MusuiEssentials::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateRouletteTask($this, $session), 1);
    }

    public function closeCrate(): void
    {
        if (!$this->isOpened) return;

        $this->getPosition()->getWorld()->broadcastPacketToViewers($this->getPosition(), BlockEventPacket::create(BlockPosition::fromVector3($this->getPosition()->asVector3()), 1, 0));
        $this->isOpened = false;
    }

    public function getDrop(int $amount): array
    {
        $dropTable = [];
        foreach ($this->getLoots() as $drop) {
            for ($i = 0; $i < $drop->getChance(); $i++) {
                $dropTable[] = $drop;
            }
        }

        $keys = array_rand($dropTable, $amount);
        if (!is_array($keys)) $keys = [$keys];
        return array_map(function ($key) use ($dropTable) {
            return $dropTable[$key];
        }, $keys);
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isValidKey(Item $item): bool
    {
        return $this->getCrate()->getName() === $item->getNamedTag()->getString("key", "none");
    }
}
