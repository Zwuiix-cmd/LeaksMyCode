<?php

namespace Zwuiix\AdvancedFreeze\Compement;

use Zwuiix\AdvancedFreeze\Compement\PacketSend;
use Zwuiix\AdvancedFreeze\Compement\ZippedResourcePackEncrypted;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackInfoEntry;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ResourcePackException;
use pocketmine\utils\Config;
use Zwuiix\AdvancedFreeze\Main;
use Webmozart\PathUtil\Path;

class ResourcePackManager
{

    public static int $PACK_CHUNK_SIZE = 128 * 1024; //128KB

    private static ResourcePackManager $instance;
    /** @var string */
    private string $path;
    /**
     * @var PacketSend[]
     */
    public static array $packSend = [];

    /** @var ResourcePack[] */
    private array $resourcePacks = [];

    /** @var ResourcePack[] */
    private array $uuidList = [];
    /**
     * @var ResourcePackInfoEntry[]
     */
    private array $resourcePackEntries = [];

    /**
     * @param string $path Path to resource-packs directory.
     */
    public function __construct(Main $pl, string $path, \Logger $logger)
    {
        self::$instance = $this;
        $this->path = $path;

        if (!file_exists($this->path)) {
            $logger->debug("Resource packs path $path does not exist, creating directory");
            mkdir($this->path);
        } elseif (!is_dir($this->path)) {
            throw new \InvalidArgumentException("Resource packs path $path exists and is not a directory");
        }

        $resourcePacksYml = Path::join($this->path, "resource_packs.yml");
        $pl->saveResource("resource_packs.yml");


        $resourcePacksConfig = new Config($resourcePacksYml, Config::YAML, []);

        $logger->info("Loading resource packs...");

        $resourceStack = $resourcePacksConfig->get("resource_stack", []);
        self::$PACK_CHUNK_SIZE = intval($resourcePacksConfig->get("pack_chunk_size", 128));
        if (self::$PACK_CHUNK_SIZE <= 0) {
            self::$PACK_CHUNK_SIZE = 64;
        }
        self::$PACK_CHUNK_SIZE *= 1024;
        if (!is_array($resourceStack)) {
            throw new \InvalidArgumentException("\"resource_stack\" key should contain a list of pack names");
        }

        foreach ($resourceStack as $pos => $pack) {
            $path = $pack["path"];
            if (!is_string($path) && !is_int($path) && !is_float($path)) {
                $logger->critical("Found invalid entry in resource pack list at offset $pos of type " . gettype($pack));
                continue;
            }
            $path = (string)$path;
            try {
                $packPath = Path::join($this->path, $path);
                if (!file_exists($packPath)) {
                    throw new ResourcePackException("File or directory not found");
                }
                if (is_dir($packPath)) {
                    throw new ResourcePackException("Directory resource packs are unsupported");
                }

                $newPack = null;
                //Detect the type of resource pack.
                $info = new \SplFileInfo($packPath);
                switch ($info->getExtension()) {
                    case "zip":
                    case "mcpack":
                        $newPack = new ZippedResourcePackEncrypted($packPath);
                        break;
                }

                if ($newPack instanceof ZippedResourcePackEncrypted) {
                    $newPack->setEncryptKey($pack['encryptKey'] ?? "");
                    $this->resourcePacks[] = $newPack;
                    $this->uuidList[strtolower($newPack->getPackId())] = $newPack;
                } else {
                    throw new ResourcePackException("Format not recognized");
                }
            } catch (ResourcePackException $e) {
                $logger->critical("Could not load resource pack \"$path\": " . $e->getMessage());
            }
        }

        $logger->debug("Successfully loaded " . count($this->resourcePacks) . " resource packs");

        foreach ($this->getResourceStack() as $pack) {
            if (!($pack instanceof ZippedResourcePackEncrypted)) continue;
            $this->resourcePackEntries[] = new ResourcePackInfoEntry($pack->getPackId(), $pack->getPackVersion(), $pack->getPackSize(), $pack->getEncryptKey(), "", $pack->getEncryptKey() !== "" ? $pack->getPackId() : "", false);
        }
    }

    /**
     * @return ResourcePackInfoEntry[]
     */
    public function getResourcePackEntries(): array
    {
        return $this->resourcePackEntries;
    }

    /**
     * Returns the directory which resource packs are loaded from.
     */
    public function getPath(): string
    {
        return $this->path . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns an array of resource packs in use, sorted in order of priority.
     * @return ResourcePack[]
     */
    public function getResourceStack(): array
    {
        return $this->resourcePacks;
    }

    /**
     * Returns the resource pack matching the specified UUID string, or null if the ID was not recognized.
     */
    public function getPackById(string $id): ?ResourcePack
    {
        return $this->uuidList[strtolower($id)] ?? null;
    }

    /**
     * Returns an array of pack IDs for packs currently in use.
     * @return string[]
     */
    public function getPackIdList(): array
    {
        return array_keys($this->uuidList);
    }

    /**
     * @return ResourcePackManager
     */
    public static function getInstance(): ResourcePackManager
    {
        return self::$instance;
    }
}
