<?php

namespace MusuiScanner\Zwuiix\task;

use MusuiScanner\Zwuiix\inventory\OfflineInventory;
use MusuiScanner\Zwuiix\Plugin;
use pocketmine\block\tile\Container;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;

class ScannerTask extends Task
{
    private const INVENTORY = 0;
    private const ENDERCHEST = 1;

    protected array $chunks = [];
    protected array $players = [];

    protected array $flags = [
        "map" => false,
        "inventory" => false,
        "enderchests" => false,
    ];

    protected int $thisChunk = 0;
    protected int $thisPlayerInventory = 0;
    protected int $thisPlayerEnderchests = 0;

    protected int $findedItem = 0;
    protected int $startedTime;

    /**
     * @param World $world
     * @param bool $map
     * @param bool $inventory
     * @param bool $enderchests
     * @param array $items
     * @param int $count
     * @param bool $delete
     */
    public function __construct(
        protected World $world,
        protected bool $map,
        protected bool $inventory,
        protected bool $enderchests,
        protected array $items,
        protected int $count,
        protected bool $delete
    )
    {
        $this->startedTime = time();
        if($this->inventory || $this->enderchests) {
            foreach (scandir(Path::join(Server::getInstance()->getDataPath(), "players")) as $item => $value){
                if(!str_contains($value, ".dat")) {
                    continue;
                }

                $name = str_replace(".dat", "", $value);
                $player = Server::getInstance()->getOfflinePlayer($name);
                $this->players[] = $player->getName();
            }
        }

        if($this->map) {
            $provider = $this->world->getProvider();
            foreach($provider->getAllChunks(true) as $coords => $chunk){
                $this->chunks[] = ["ChunkX" => $coords[0], "ChunkZ" => $coords[1]];
            }
        }

        Plugin::getInstance()->getLoader()->getScheduler()->scheduleRepeatingTask($this, 2);
    }

    /**
     * @return void
     */
    public function onRun(): void
    {
        if($this->inventory && !$this->flags["inventory"]) {
            if ($this->thisPlayerInventory < count($this->players)) {
                $data = Server::getInstance()->getOfflinePlayerData(strtolower($this->players[$this->thisPlayerInventory])) ?? new CompoundTag();
                $this->scanDataForFind(strtolower($this->players[$this->thisPlayerInventory]), $data, $this::INVENTORY);
                $this->thisPlayerInventory++;

                $time = round($this->thisPlayerInventory * 100 / count($this->players));
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    if($onlinePlayer->hasPermission("musuiscanner.command")) {
                        $onlinePlayer->sendTip(TextFormat::GREEN . "Inventory scan in progress... §7(%{$time}%)");
                    }
                }

                return;
            }
            $this->flags["inventory"] = true;
            return;
        }

        if($this->inventory && !$this->flags["enderchests"]) {
            if ($this->thisPlayerEnderchests < count($this->players)) {
                $data = Server::getInstance()->getOfflinePlayerData(strtolower($this->players[$this->thisPlayerEnderchests])) ?? new CompoundTag();
                $this->scanDataForFind(strtolower($this->players[$this->thisPlayerEnderchests]), $data, $this::ENDERCHEST);
                $this->thisPlayerEnderchests++;

                $time = round($this->thisPlayerEnderchests * 100 / count($this->players));
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    if($onlinePlayer->hasPermission("musuiscanner.command")) {
                        $onlinePlayer->sendTip(TextFormat::GREEN . "EnderChests scan in progress... §7(%{$time}%)");
                    }
                }

                return;
            }
            $this->flags["enderchests"] = true;
            return;
        }

        if($this->map && !$this->flags["map"]) {
            if($this->thisChunk < count($this->chunks)) {
                $chunk = $this->chunks[$this->thisChunk];
                $world = $this->world;
                $world->loadChunk($chunk["ChunkX"], $chunk["ChunkZ"]);
                $chunk = $world->getChunk($chunk["ChunkX"], $chunk["ChunkZ"]);
                if($chunk instanceof Chunk) {
                    foreach ($chunk->getTiles() as $tile) {
                        $pos = $tile->getPosition();
                        if(!$tile instanceof Container) {
                            continue;
                        }

                        foreach ($tile->getInventory()->getContents() as $content) {
                            foreach ($this->items as $item) {
                                if($content->equals($item, false, false) && $content->getCount() >= $this->count) {
                                    $this->findedItem++;
                                    $tile->getInventory()->remove($content);
                                }
                            }
                        }
                    }
                }

                $time = round($this->thisChunk * 100 / count($this->chunks));
                foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                    if($onlinePlayer->hasPermission("musuiscanner.command")) {
                        $onlinePlayer->sendTip(TextFormat::GREEN . "Map scan in progress... §7(%{$time}%)");
                    }
                }

                $this->thisChunk++;
                return;
            }
            $this->flags["map"] = true;
            return;
        }

        $time = (time() - $this->startedTime);
        Server::getInstance()->getLogger()->alert("[SCANNER] Scan completed, {$this->findedItem} items found in {$time} second(s).");
        $this->getHandler()->cancel();
    }

    /**
     * @param string $name
     * @param CompoundTag $data
     * @param int $type
     * @return void
     */
    public function scanDataForFind(string $name, CompoundTag $data, int $type = self::INVENTORY): void
    {
        $class = OfflineInventory::fromOfflinePlayerData($data);

        if($this::INVENTORY === $type) {
            $inventory = $class->readInventory();
            $armorInventory = $class->readArmorInventory();
            $offhand = $class->readOffhandItem();

            foreach ($this->items as $item) {
                if($item->equals($offhand, false, false) && $offhand->getCount() >= $this->count) {
                    $this->findedItem += $item->getCount();
                    $offhand = VanillaItems::AIR();
                }
            }

            $cleanInventoryItems = [];
            foreach ($inventory as $i => $item) {
                if(!$item instanceof Item) {
                    continue;
                }

                $find = false;
                foreach ($this->items as $value) {
                    if($item->equals($value, false, false) && $item->getCount() >= $this->count) {
                        $this->findedItem += $item->getCount();
                        $find = true;
                    }
                }
                if(!$find) {
                    $cleanInventoryItems[$i] = $item;
                }
            }

            $cleanArmorInventoryItems = [];
            foreach ($armorInventory as $i => $item) {
                if(!$item instanceof Item) {
                    continue;
                }

                $find = false;
                foreach ($this->items as $value) {
                    if($item->equals($value, false, false) && $item->getCount() >= $this->count) {
                        $this->findedItem += $item->getCount();
                        $find = true;
                    }
                }
                if(!$find) {
                    $cleanArmorInventoryItems[$i] = $item;
                }
            }

            $class->writeInventory($cleanInventoryItems);
            $class->writeArmorInventory($cleanArmorInventoryItems);
            $class->writeOffhandItem($offhand);
        } else if($this::ENDERCHEST === $type) {
            $enderchest = $class->readEnderInventory();

            $cleanEnderchestItems = [];
            foreach ($enderchest as $i => $item) {
                if(!$item instanceof Item) {
                    continue;
                }

                $find = false;
                foreach ($this->items as $value) {
                    if($item->equals($value, false, false) && $item->getCount() >= $this->count) {
                        $this->findedItem += $item->getCount();
                        $find = true;
                    }
                }
                if(!$find) {
                    $cleanEnderchestItems[$i] = $item;
                }
            }

            $class->writeEnderInventory($cleanEnderchestItems);
        }

        Server::getInstance()->saveOfflinePlayerData(strtolower($name), $class->getOfflinePlayerData());
    }
}