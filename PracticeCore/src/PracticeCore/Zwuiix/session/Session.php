<?php

namespace PracticeCore\Zwuiix\session;

use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use PracticeCore\Zwuiix\ffa\FFA;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\kit\LobbyKit;
use PracticeCore\Zwuiix\scoreboard\Scoreboard;

class Session extends Player
{
    public const TAG_ENDER_PEARL_COOLDOWN = "practicecore.ender_pearl";
    public const TAG_COMBAT_LOGGER_COOLDOWN = "practicecore.combat_logger";
    public const TAG_CHAT_COOLDOWN = "practicecore.chat";

    /*** @var Cooldown[] */
    protected array $cooldown = array();
    protected Scoreboard $scoreboard;
    protected Info $info;
    protected Cps $cps;
    protected ?Session $opponent = null;
    protected ?Session $replySession = null;
    protected ?Vector3 $lastDamageVector = null;
    protected ?string $disguise = null;
    protected string $lastMessage = "";
    protected ?FFA $ffa = null;

    /**
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $playerInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $playerInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag)
    {
        parent::__construct($server, $session, $playerInfo, $authenticated, $spawnLocation, $namedtag);
        $this->addCooldown(self::TAG_ENDER_PEARL_COOLDOWN, new CooldownTick($this));
        $this->addCooldown(self::TAG_COMBAT_LOGGER_COOLDOWN, new CooldownTick($this));
        $this->addCooldown(self::TAG_CHAT_COOLDOWN, new CooldownTick($this));
        $this->scoreboard = new Scoreboard();
        $this->info = new Info($this, $namedtag ?? new CompoundTag());
        $this->cps = new Cps($this);

        //$this->effectManager = new EffectManager($this);
    }

    public function doFirstSpawn(): void
    {
        parent::doFirstSpawn();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(!$onlinePlayer instanceof Session) continue;
            if($onlinePlayer->isDisguise() && $onlinePlayer->getDisguiseName() === $this->getName()) {
                $onlinePlayer->setDisguise(null);
                $onlinePlayer->getInfo()->update();
                break;
            }
        }
        $this->getInfo()->update();
    }

    /**
     * @return CompoundTag
     */
    public function getSaveData(): CompoundTag
    {
        return $this->getInfo()->destruct(parent::getSaveData());
    }

    /**
     * @param string $name
     * @param Cooldown $cooldown
     * @return void
     */
    public function addCooldown(string $name, Cooldown $cooldown): void
    {
        if(isset($this->cooldown[$name])) return;
        $this->cooldown[$name] = $cooldown;
    }

    /**
     * @param string $name
     * @return Cooldown|null
     */
    public function getCooldownByName(string $name): ?Cooldown
    {
        return $this->cooldown[$name] ?? null;
    }

    /**
     * @return Cooldown[]
     */
    public function getAllCooldown(): array
    {
        return $this->cooldown;
    }

    public function getScoreboard(): Scoreboard
    {
        return $this->scoreboard;
    }

    /**
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }

    /**
     * @return Cps
     */
    public function getCps(): Cps
    {
        return $this->cps;
    }

    /**
     * @return bool
     */
    public function isInFFA(): bool
    {
        return !is_null($this->ffa);
    }

    /**
     * @return FFA|null
     */
    public function getFfa(): ?FFA
    {
        return $this->ffa;
    }

    /**
     * @param FFA|null $ffa
     * @return void
     */
    public function setFfa(?FFA $ffa): void
    {
        $this->ffa = $ffa;
    }

    public function hasLastDamageVector(): bool
    {
        return $this->lastDamageVector !== null;
    }

    public function getLastDamageVector(): Vector3
    {
        return $this->lastDamageVector;
    }

    /**
     * @param Vector3 $vector3
     * @return void
     */
    public function setLastDamageVector(Vector3 $vector3): void
    {
        $this->lastDamageVector = $vector3;
    }

    /**
     * @return bool
     */
    public function hasReplySession(): bool
    {
        return !is_null($this->replySession);
    }

    /**
     * @return Session|null
     */
    public function getReplySession(): ?Session
    {
        return $this->replySession;
    }

    /**
     * @param ?Session $session
     * @return void
     */
    public function setReplySession(?Session $session): void
    {
        $this->replySession = $session;
    }

    /**
     * @return bool
     */
    public function hasOpponent(): bool
    {
        return !is_null($this->opponent);
    }

    /**
     * @return Session|null
     */
    public function getOpponent(): ?Session
    {
        return $this->opponent;
    }

    /**
     * @param ?Session $session
     * @return void
     */
    public function setOpponent(?Session $session): void
    {
        $this->opponent = $session;
    }

    /**
     * @return string|null
     */
    public function isDisguise(): ?string
    {
        return !is_null($this->disguise);
    }

    /**
     * @return string|null
     */
    public function getDisguiseName(): ?string
    {
        return $this->disguise;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setDisguise(?string $name): void
    {
        $this->disguise = $name;
    }

    /**
     * @return string
     */
    public function getLastMessage(): string
    {
        return $this->lastMessage;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setLastMessage(string $name): void
    {
        $this->lastMessage = $name;
    }

    /**
     * @param float $x
     * @param float $z
     * @param float $force
     * @param float|null $verticalLimit
     * @return void
     */
    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
        $knockback = KnockbackHandler::getInstance()->getKnockback();
        $bXZ = $knockback->getX();
        $bY = $knockback->getY();

        $heightLimiter = KnockbackHandler::getInstance()->getHeightLimiter();
        if ($this->hasLastDamageVector()) {
            $position = $this->getLastDamageVector();
            $dist = $this->getPosition()->getY() - $position->getY();
            $addDist = $dist + 0.5;
            if (!$this->isOnGround()) {
                $bool = $addDist > $heightLimiter->getZ();
                $diff = $bool ? $heightLimiter->getX() : $heightLimiter->getY();
                $bY -= $dist * $diff;
            }
        }

        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) return;

        if (mt_rand() / mt_getrandmax() > AttributeFactory::getInstance()->mustGet(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motionX = $this->motion->x / 2;
            $motionY = $this->motion->y / 2;
            $motionZ = $this->motion->z / 2;
            $motionX += $x * $f * $bXZ;
            $motionY += $bY;
            $motionZ += $z * $f * $bXZ;

            $this->setMotion(new Vector3($motionX, $motionY > $verticalLimit ? $verticalLimit : $motionY , $motionZ));
        }
    }


    /**
     * @param Vector3 $pos
     * @param float|null $yaw
     * @param float|null $pitch
     * @param int $mode
     * @return bool
     */
    public function teleport(Vector3 $pos, ?float $yaw = null, ?float $pitch = null, int $mode = MovePlayerPacket::MODE_TELEPORT) : bool
    {
        if(parent::teleport($pos, $yaw, $pitch)){

            $this->removeCurrentWindow();
            $this->stopSleep();

            $this->sendPosition($this->location, $this->location->yaw, $this->location->pitch, $mode);
            $this->broadcastMovement(true);

            $this->spawnToAll();

            $this->resetFallDistance();
            $this->nextChunkOrderRun = 0;
            if($this->spawnChunkLoadCount !== -1){
                $this->spawnChunkLoadCount = 0;
            }
            $this->blockBreakHandler = null;

            $this->resetLastMovements();

            return true;
        }

        return false;
    }

    /**
     * @param array $players
     * @param array $packets
     * @param bool $without_session
     * @return void
     */
    public function broadcastPackets(array $players, array $packets, bool $without_session = true): void
    {
        if(!$without_session) $players[] = $this;

        foreach ($players as $player) {
            if(!$player instanceof Session) continue;
            foreach ($packets as $packet) {
                if(!$packet instanceof ClientboundPacket) continue;
                $player->getNetworkSession()->sendDataPacket($packet);
            }
        }
    }

    /**
     * @param Item $item
     * @return int
     */
    public function countItems(Item $item): int
    {
        return count($this->getInventory()->all($item));
    }

    public function onDeath(): void
    {
        $this->getCooldownByName(self::TAG_COMBAT_LOGGER_COOLDOWN)->setCooldown(false, false);
        $this->getCooldownByName(self::TAG_ENDER_PEARL_COOLDOWN)->setCooldown(false, false);
        $this->setOpponent(null);
        $this->getInfo()->resetKillStreak();
        $this->getInfo()->addDeath();
        parent::onDeath();
    }

    /**
     * @return void
     */
    public function spawn(): void
    {
        $this->setFfa(null);
        LobbyKit::getInstance()->give($this);
        $this->getCooldownByName(self::TAG_COMBAT_LOGGER_COOLDOWN)->setCooldown(false, false);
        $this->getCooldownByName(self::TAG_ENDER_PEARL_COOLDOWN)->setCooldown(false, false);
        $this->setOpponent(null);
        $this->getXpManager()->setXpLevel(0);
        $this->getXpManager()->setXpProgress(0);

        $this->setGamemode(GameMode::ADVENTURE());
        $this->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
    }

    /**
     * @param Session $damager
     * @return void
     */
    public function effectKill(Session $damager): void
    {
        $lightning = new AddActorPacket();
        $lightning->actorUniqueId = Entity::nextRuntimeId();
        $lightning->actorRuntimeId = $lightning->actorUniqueId;
        $lightning->type = 'minecraft:lightning_bolt';
        $lightning->position = $this->getLocation()->asVector3();
        $lightning->motion = null;
        $lightning->pitch = $this->getLocation()->getPitch();
        $lightning->yaw = $this->getLocation()->getYaw();
        $lightning->headYaw = 0.0;
        $lightning->attributes = [];
        $lightning->metadata = [];
        $lightning->syncedProperties=new PropertySyncData([], []);
        $lightning->links = [];

        $thunder = new PlaySoundPacket();
        $thunder->soundName = 'ambient.weather.thunder';
        $thunder->x = $this->getLocation()->getX();
        $thunder->y = $this->getLocation()->getY();
        $thunder->z = $this->getLocation()->getZ();
        $thunder->volume = 0.5;
        $thunder->pitch = 1;

        $damager->getNetworkSession()->sendDataPacket($lightning);
        $damager->getNetworkSession()->sendDataPacket($thunder);
    }
}