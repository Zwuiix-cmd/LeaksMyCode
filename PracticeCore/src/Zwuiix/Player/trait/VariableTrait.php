<?php

namespace Zwuiix\Player\trait;

use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\world\Position;
use Zwuiix\Handler\Invest;
use Zwuiix\Handler\Mods;
use Zwuiix\Handler\Rank;
use Zwuiix\Handler\Staff;
use Zwuiix\Player\sub\Data;
use Zwuiix\Player\sub\UserInfo;
use Zwuiix\Player\User;

trait VariableTrait
{
    public float $timerLastTimestamp = -1.0;
    public float $timerBalance = 0.0;
    public int $timerViolations = 0;
    public int $aimViolations = 0;
    public int $killAuraViolations = 0;
    public int $timerWait = -1;
    public int $timerAWait = -1;
    public int $timerBWait = -1;

    public int $killAuraAWait = -1;
    public bool $killAuraEntitySpawned = false;

    public int $reachWait = -1;
    public int $reachViolations = 0;
    public int $lastPing = 0;

    public float $maxDistanceKnockBack = 3.5;

    public float $currentYawDelta = 0.0;
    public float $previousYaw = 0.0;
    public float $currentYaw = 0.0;
    public bool $isFullKeyboardGameplay = true;
    public bool $isLogsMode = false;
    public int $currentTick = 0;
    public ?Vector3 $lastPosition = null;

    private ?Position $lastDamagePosition = null;
    public int|float $lastAttackTime = 0;
    private ?Vector3 $positionPearl = null;
    public ?string $lastUserFight = null;
    public ?array $lastUserFightContent = null;
    public ?array $lastThisUserFightContent = null;
    public ?string $lastFightMessageKill = null;
    public ?string $lastFightMessageDiff = null;

    /**
     * @param Vector3 $pos
     * @param float|null $yaw
     * @param float|null $pitch
     * @param int $mode
     * @return void
     */
    public function sendPosition(Vector3 $pos, ?float $yaw = null, ?float $pitch = null, int $mode = MovePlayerPacket::MODE_NORMAL): void
    {
        parent::sendPosition($pos, $yaw, $pitch, $mode);
    }

    public function onUpdate(int $currentTick): bool
    {
        $color = $this->isOp() ? "§c" : "§a";
        $this->setNameTag( $color."{$this->getName()}");
        return parent::onUpdate($currentTick);
    }
}