<?php

namespace Zwuiix\Utils;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Zwuiix\Player\User;

class Marcel
{
    use SingletonTrait;

    public const TYPE = [
        0 => [
            "Reach" => 0,
            "TimerB" => 1,
            "TimerC" => 2,
            "AimB" => 3,
            "AimC" => 4,
            "KillAuraB" => 5,
        ],
        1 => [
            0 => "Reach",
            1 => "TimerB",
            2 => "TimerC",
            3 => "AimB",
            4 => "AimC",
            5 => "KillAuraB",
        ]
    ];

    /**
     * @param int $type
     * @param User $user
     * @param User|null $damaged
     * @param int $violation
     * @param float|int|string $details
     * @return string|null
     */
    public function formatViolationInfo(int $type, User $user, ?User $damaged, int $violation, float|int|string $details = 0.0): ?string
    {
        $base="§7[Marcel] §4{$user->getName()} §cviolated ";
        switch ($type){
            case self::TYPE[0]["Reach"]:
                if(!$damaged instanceof User)return null;

                $positionUser=$user->getPosition();
                $positionDamaged=$damaged->getPosition();

                [$formatX, $formatY, $formatZ] = [
                    $positionUser->x <= $positionDamaged->x ? $positionUser->x - $positionDamaged->x : $positionDamaged->x - $positionUser->x,
                    $positionUser->y <= $positionDamaged->y ? $positionUser->y - $positionDamaged->y : $positionDamaged->y - $positionUser->y,
                    $positionUser->z <= $positionDamaged->z ? $positionUser->z - $positionDamaged->z : $positionDamaged->z - $positionUser->z,
                ];
                [$xdiff, $ydiff, $zdiff] = [$formatX, $formatY, $formatZ];

                $pingUser=$user->getNetworkSession()->getPing();
                $pingDamaged=$damaged->getNetworkSession()->getPing();
                $pingDiff = $pingUser <= $pingDamaged ? $pingUser - $pingDamaged : $pingDamaged - $pingUser;

                return $base . "Reach §7[dist={$details}] [diffX={$xdiff}/diffY={$ydiff}/diffZ={$zdiff}] [ping={$pingUser}/pingDiff={$pingDiff}] [x{$violation}]";
            case self::TYPE[0]["TimerB"]:
                return $base . "TimerB §7[packets={$details}] [ping={$user->getNetworkSession()->getPing()}] [x{$violation}]";
            case self::TYPE[0]["TimerC"]:
                return $base . "TimerC §7[packets={$details}] [ping={$user->getNetworkSession()->getPing()}] [x{$violation}]";
            case self::TYPE[0]["AimB"]:
                return $base . "AimB §7[diff={$details}] [ping={$user->getNetworkSession()->getPing()}] [x{$violation}]";
            case self::TYPE[0]["AimC"]:
                return $base . "AimC §7[diff={$details}] [ping={$user->getNetworkSession()->getPing()}] [x{$violation}]";
            case self::TYPE[0]["KillAuraB"]:
                return $base . "KillAuraB §7[tickDiff={$details}] [ping={$user->getNetworkSession()->getPing()}] [x{$violation}]";
        }
        return null;
    }

    public function hasReach(User $damager, User $player, EntityDamageByEntityEvent $event): bool
    {
        if ($event->isCancelled()) return false;

        $damagerPing = $damager->getNetworkSession()->getPing();
        $playerPing = $player->getNetworkSession()->getPing();

        $distance = $player->getEyePos()->distance(new Vector3($damager->getEyePos()->getX(), $player->getEyePos()->getY(), $damager->getEyePos()->getZ()));
        $distance -= $damagerPing * 0.0041;
        $distance -= $playerPing * 0.0051;

        if ($distance < 1) {
            return false;
        }

        if ($player->isSprinting()) {
            $distance -= 0.97;
        } else {
            $distance -= 0.87;
        }

        if ($damager->isSprinting()) {
            $distance -= 0.77;
        } else {
            $distance -= 0.67;
        }

        if ($distance > 5) {
            $event->cancel();
            return false;
        }

        if ($distance > 3.0) {
            if (time() - $damager->reachWait < 1) {
                return false;
            }
            $damager->reachWait = time();
            $detail = round($distance, 3);

            if ($damager->reachViolations >= 3) {
                //Server::getInstance()->broadcastMessage($this->formatViolationInfo(self::TYPE[0]["Reach"], $damager, $player, $damager->reachViolations, $detail));
            }

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return true
     */
    public function hasTimer(User $user): bool
    {
        if(!$user->isConnected())return false;
        if(!$user->isAlive()){
            $user->timerLastTimestamp = -1.0;
            return false;
        }
        $timestamp = microtime(true);
        if ($user->timerLastTimestamp === -1.0) {
            $user->timerLastTimestamp = $timestamp;
            return false;
        }
        $diff = $timestamp - $user->timerLastTimestamp;

        $user->timerBalance += 0.05;
        $user->timerBalance -= $diff;

        if ($user->timerBalance >= 0.25) {
            $user->timerBalance = 0.0;

            if (time() - $user->timerWait < 1) {
                return false;
            }
            $user->timerWait = time();
            $user->timerViolations++;

            //Server::getInstance()->broadcastMessage($this->formatViolationInfo(self::TYPE[0]["Reach"], $user, null, $user->timerViolations, round($diff, 3)));
            return true;
        }

        $user->timerBalance = 0.0;
        $user->timerLastTimestamp = $timestamp;
        return false;
    }

    public function informStaff(string $log): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer){
            if(!$onlinePlayer instanceof User)continue;
            if(!$onlinePlayer->isLogsMode) continue;
            $onlinePlayer->sendMessage($log);
        }
    }
}