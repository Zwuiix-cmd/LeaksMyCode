<?php

namespace MusuiAntiCheat\Zwuiix\session;

use JsonException;
use MusuiAntiCheat\Zwuiix\event\SessionCheatFlagged;
use MusuiAntiCheat\Zwuiix\handler\BanHandler;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\handler\WebhookHandler;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\utils\MovementConstants;
use MusuiAntiCheat\Zwuiix\utils\ReflectionUtils;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\CorrectPlayerMovePredictionPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\types\PlayerBlockAction;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use pocketmine\timings\TimingsHandler;
use pocketmine\world\Position;
use ReflectionException;

class Session
{
    public TimingsHandler $timings;
    private static ?Vector3 $ZERO_VECTOR = null;
    public ?float $lastPacketReceive = null;
    public float $balance = 0;
    public float $flyingTicks = 0;
    public bool $isInLoadedChunk = false;

    public Vector3 $currentLocation, $lastLocation;
    public Vector3 $currentMoveDelta, $lastMoveDelta;
    public float $currentYaw = 0.0, $previousYaw = 0.0, $currentPitch = 0.0, $previousPitch = 0.0;
    public float $currentYawDelta = 0.0, $lastYawDelta = 0.0, $currentPitchDelta = 0.0, $lastPitchDelta = 0.0;

    public bool $isInVoid = false;
    public float $glidingTicks = 0;
    public float $lastAttackTicks = 0;
    public float $teleportTicks = 0;
    public float $motionTicks = 0;
    public bool $waiting = false;
    public float $currentTick = 0;
    public int $lastTarget = 0;
    public int $target = 0;
    public float $attackTick = 0;
    public float $ySize = 0;
    public ?Vector3 $attackPos = null;
    public int|float $lastTick = 0;
    public float $gravity = MovementConstants::NORMAL_GRAVITY;
    public array $logs = array();
    public bool $blacklisted = false;
    public bool $inventoryClose = true;
    public int $inventoryLastCloseTicks = 0;
    public ?int $lastPlayerAuthInputFlags = null;
    public float $moveForward = 0;
    public float $moveStrafe = 0;
    public Vector3 $lastMotion;
    public int $liquidTicks = 0;
    public int $cobwebTicks = 0;
    public int $climbableTicks = 0;
    public array $pressedKeys = [];
    public bool $isSprinting = false;
    public bool $isSneaking = false;
    public ?Location $lastNoSuffocateLocation = null;
    public float $jumpMovementFactor = MovementConstants::JUMP_MOVE_NORMAL;
    public int $lastClickTicks = 0;
    public ?Location $lastLocationNoClientPredictions = null;
    protected array $forms = [];

    public float $jumped = 0;
    public ?Vector3 $lastGroundPosition = null;
    public float $lastJumpTicks = 0;
    public ?Vector3 $lastGroundDelta = null;
    public int $lastGroundTick = 0;
    public float $latency = 0;

    /*** @var PlayerBlockAction[] */
    public array $lastBlockActions = array();
    public bool $logsStatus = false;

    protected ?UserInfo $userInfo = null;
    protected ?CpsManager $cpsManager = null;
    protected array $waitings = [];
    public int|float $lastDeathTicks = 0;
    private array $tickSync = [];

    /**
     * @param Player $player
     */
    public function __construct(
        protected Player $player
    ) {
        if (self::$ZERO_VECTOR === null) {
            self::$ZERO_VECTOR = new Vector3(0, 0, 0);
        }

        $this->blacklisted = in_array($this->player->getName(), Main::getInstance()->getBlacklist()->get("sessions", []));
        $this->timings = new TimingsHandler("{$this->player->getName()}'s Session");

        $this->currentLocation = $this->player->getLocation();
        $this->lastLocation = $this->player->getLocation();
        $this->lastGroundPosition = $this->player->getLocation();
        $this->currentMoveDelta = self::$ZERO_VECTOR;
        $this->lastMoveDelta = self::$ZERO_VECTOR;
        $this->lastMotion = self::$ZERO_VECTOR;
    }

    /**
     * @return UserInfo
     */
    public function getUserInfo(): UserInfo
    {
        return $this->userInfo  ?? $this->userInfo = new UserInfo($this);
    }

    /**
     * @return CpsManager
     */
    public function getCpsManager(): CpsManager
    {
        return $this->cpsManager  ?? $this->cpsManager = new CpsManager($this);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return NetworkSession
     */
    public function getNetwork(): NetworkSession
    {
        return $this->getPlayer()->getNetworkSession();
    }

    /**
     * @return PlayerInfo|null
     */
    public function getPlayerInfo(): ?PlayerInfo
    {
        return $this->getNetwork()->getPlayerInfo();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->getPlayerInfo()->getExtraData();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return "{$this->getPlayer()->getName()}:{$this->getNetwork()->getPort()}";
    }

    /*** @return TimingsHandler */
    public function getTimings() : TimingsHandler
    {
        return $this->timings;
    }

    /**
     * @param Module $module
     * @param array $options
     * @param bool $back
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    public function flag(Module $module, array $options, bool $back = false): void
    {
        if(!$this->getPlayer()->isConnected()) return;
        if(!$this->getPlayer()->spawned) return;
        if(isset($this->logs[$module->getName() . $module->getType()][date("d/m/Y h:m:s")]))  {
            return;
        }

        if(BanHandler::getInstance()->isBanned($this->getPlayer()->getName())) {
            BanHandler::getInstance()->canConnect($this);
            return;
        }

        $ev = new SessionCheatFlagged($this, $module, $options);
        if($ev->isCancelled()) return;

        $module->detect();
        $vl = isset($this->logs[$module->getName() . $module->getType()]) ? count($this->logs[$module->getName() . $module->getType()]) : 0;
        $vl += 1;
        $details = LanguageHandler::getInstance()->translate("logs_details", [$module->getName(), $module->getType(), $vl, implode(", ", $options)]);

        $this->logs[$module->getName() . $module->getType()][date("d/m/Y h:m:s")] = $details;
        $options[] = "type={$module->getName()}({$module->getType()})";

        $d = implode(", ", $options);
        if(WebhookHandler::getInstance()->logs) {
            $url = WebhookHandler::getInstance()->url;
            if($url !== "none") {
                $username = $this->player->getName();
                $json = str_replace([
                    "&{BASE64_INFO}",
                    "&{USERNAME}",
                    "&{MODULE}",
                    "&{VIOLATIONS}",
                    "&{MAX_VIOLATIONS}",
                    "&{DETAILS}",
                ], [
                    base64_encode("{$username}:{$module->getName()}{$module->getType()}:{$vl}:{$module->getMaxVL()}:[{$d}]"),
                    $username,
                    "{$module->getName()}{$module->getType()}",
                    $vl,
                    $module->getMaxVL(),
                    $d
                ], WebhookHandler::getInstance()->getWebhook(WebhookHandler::LOGS));
                WebhookHandler::getInstance()->send($url, $json);
            }
        }

        $format = LanguageHandler::getInstance()->translate("logs", [$this->getPlayer()->getName(), $details]);
        foreach (SessionManager::getInstance()->getAll() as $session) {
            if($session->hasLogs()) {
                $session->getPlayer()->sendMessage($format);
            }
        }

        if($back) {
            $this->back($module->options("back", "instant"));
        }

        $noBan = $module->getMaxVL() == -1;
        if($vl >= $module->getMaxVL() && !$noBan) {
            $flag = Main::getInstance()->getData()->getNested("options.ban", "default");
            $server = Server::getInstance();
            if(strtolower($flag) === "default") {
                BanHandler::getInstance()->ban($this, "Musui", "Unfair Advantage ({$module->getName()}{$module->getType()})", implode(", ", $options));
            } else $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), str_replace(["{PLAYER}", "{REASON}", "{DETAIL}"], [$this->getPlayerName(), "{$module->getName()}", implode(", ", $options)], $flag));
            $server->getNetwork()->blockAddress($this->getNetwork()->getIp(), 15);

            if(WebhookHandler::getInstance()->ban) {
                $url = WebhookHandler::getInstance()->url;
                if($url !== "none") {
                    $username = $this->player->getName();
                    $json = str_replace([
                        "&{BASE64_INFO}",
                        "&{USERNAME}",
                        "&{MODULE}",
                        "&{VIOLATIONS}",
                        "&{MAX_VIOLATIONS}",
                        "&{DETAILS}",
                    ], [
                        base64_encode("{$username}:{$module->getName()}{$module->getType()}:{$vl}:{$module->getMaxVL()}:[{$d}]"),
                        $username,
                        "{$module->getName()}{$module->getType()}",
                        $vl,
                        $module->getMaxVL(),
                        $d
                    ], WebhookHandler::getInstance()->getWebhook(WebhookHandler::BAN));
                    WebhookHandler::getInstance()->send($url, $json);
                }
            }
        }
    }

    /**
     * @param string $type
     * @param Position|null $position
     * @return void
     * @throws ReflectionException
     */
    public function back(string $type, Vector3 $position = null): void
    {
        $position = is_null($position) ? ($this->player->onGround ? $this->lastLocation : $this->lastGroundPosition): $position;
        switch ($type) {
            case "instant":
                ReflectionUtils::invoke(Player::class, $this->player, "sendPosition", $position, null, null, MovePlayerPacket::MODE_NORMAL);
                break;
            case "smooth":
                if($position->distance($this->player->getPosition()) <= 15) {
                    $this->back("instant", $position);
                    break;
                }
                ReflectionUtils::invoke(Player::class, $this->player, "setPosition", $position);

                $pk = CorrectPlayerMovePredictionPacket::create($position, $this->lastGroundDelta ?? new Vector3(0, -0.08 * 0.98, 0), true, $this->lastGroundTick);
                $this->player->getNetworkSession()->sendDataPacket($pk);
                break;
        }

        $this->teleportTicks = 0;
    }

    public function isInsideOfSolid(Position $position = null) : bool{
        $block = is_null($position) ? $this->getPlayer()->getWorld()->getBlockAt((int) floor($this->currentLocation->x), (int) floor($y = ($this->currentLocation->y + $this->getPlayer()->getEyeHeight())), (int) floor($this->currentLocation->z)) : $position->getWorld()->getBlock($position);
        return $block->isSolid() && !$block->isTransparent() && $block->collidesWithBB($this->getPlayer()->getBoundingBox());
    }

    /**
     * @return bool
     */
    public function isBlacklist(): bool
    {
        return $this->blacklisted;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setBlacklist(bool $value = true): void
    {
        $this->blacklisted = $value;
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function addBlacklist(): void
    {
        $data = Main::getInstance()->getBlacklist()->get("sessions", []);
        $data[] = $this->player->getName();
        Main::getInstance()->getBlacklist()->set("sessions", $data);
        Main::getInstance()->getBlacklist()->save();

        $this->setBlacklist();
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function removeBlacklist(): void
    {
        $data = Main::getInstance()->getBlacklist()->get("sessions");
        foreach ($data as $i => $name) {
            if($name === $this->getPlayer()->getName()) unset($data[$i]);
        }

        Main::getInstance()->getBlacklist()->set("sessions", $data);
        Main::getInstance()->getBlacklist()->save();

        $this->setBlacklist(false);
    }

    /**
     * @param string $type
     * @param array $data
     * @return void
     */
    public function sendTranslateMessage(string $type, array $data = []): void
    {
        $this->getPlayer()->sendMessage($this->translate($type,  $data));
    }

    /**
     * @param string $type
     * @param array $data
     * @return string|array|string[]
     */
    public function translate(string $type, array $data = []): string|array
    {
        return LanguageHandler::getInstance()->translate($type, $data);
    }

    /**
     * @return float
     */
    public function getDrag(): float
    {
        return 0.02;
    }

    /**
     * @param Module $module
     * @return false|mixed
     */
    public function isWaiting(Module $module): mixed
    {
        return $this->waitings["{$module->getName()}{$module->getType()}"] ?? false;
    }

    /**
     * @param Module $module
     * @param bool $value
     * @return void
     */
    public function setWaiting(Module $module, bool $value): void
    {
        $this->waitings["{$module->getName()}{$module->getType()}"] = $value;
    }

    /**
     * @return bool
     */
    public function hasLogs(): bool
    {
        return $this->logsStatus;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setLogs(bool $value = true): void
    {
        $this->logsStatus = $value;
    }

    /**
     * @return bool
     */
    public function constantCheck(): bool
    {
        return
            $this->motionTicks >= 45 &&
            $this->teleportTicks >= 15 &&
            $this->lastDeathTicks >= 40 &&
            $this->lastAttackTicks >= 45 &&
            $this->flyingTicks >= 18 &&
            $this->glidingTicks >= 8 &&
            $this->isInLoadedChunk &&
            $this->getPlayer()->isSurvival() &&
            $this->getPlayer()->spawned &&
            !$this->isInVoid;
    }

    /**
     * @param bool $bool
     * @return void
     */
    public function setConnected(bool $bool = true): void
    {
        if($bool && Main::getInstance()->getData()->getNested("options.message_on_spawn", true)) $this->getPlayer()->sendMessage(LanguageHandler::getInstance()->translate("client_spawn"));
    }

    /**
     * @return float|int
     */
    public function getLatency(): float|int
    {
        return $this->latency;
    }

    /**
     * @param int $formId
     * @param mixed $data
     * @return void
     */
    public function addForm(int $formId, mixed $data): void
    {
        $this->forms[$formId] = $data;
    }

    /**
     * @param int $formId
     * @return mixed|null
     */
    public function getForm(int $formId): mixed
    {
        return $this->forms[$formId] ?? null;
    }

    /**
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->player->getName();
    }

    public function motion(): Vector3
    {
        return $this->getPlayer()->getMotion();
    }
}