<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\reach;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\AABB;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\Server;
use ReflectionException;

class ReachC extends Module
{
    public function __construct()
    {
        parent::__construct("Reach", "C",
            ModuleManager::generateDefaultData(
                "Allows you to check whether someone is tapping a person outside the possible attack zone.",
                -1,
                ["width" => 0.7001, "height" => 2.0001, "rawDist" => 3.81, "maxBuffer" => 3.5, "maxDist" => 5.7]
            ));
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        // TODO: https://github.com/ethaniccc/Esoteric/blob/master/src/ethaniccc/Esoteric/check/combat/range/RangeA.php
        if($packet instanceof InventoryTransactionPacket) {
            if($packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getTypeId() === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY && $session->getPlayer()->isSurvival()) {
                if($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK) {
                    $session->setWaiting($this, true);
                }
            }
        }elseif ($packet instanceof PlayerAuthInputPacket && $session->isWaiting($this)) {
            if ($session->teleportTicks <= 10) {
                return;
            }
            $target = Server::getInstance()->getWorldManager()->findEntity($session->target);
            if(!$target instanceof Entity) return;

            if(!isset($this->buffer[$session->getName()])) {
                $this->buffer[$session->getName()] = 0;
            }

            $AABB = AABB::fromPosition($target->getPosition(), 0.7001, 2.0001);
            $rawDistance = $AABB->distanceFromVector($session->attackPos);
            if ($rawDistance > $this->options("rawDist", 3.81)) {
                $this->buffer[$session->getName()]++;
                if ($this->buffer[$session->getName()] >= $this->options("maxBuffer", 3.5)) {
                    $session->flag($this, ["type=aabb", "diff={$this->buffer[$session->getName()]}", "ping={$session->getNetwork()->getPing()}ms"]);
                    $this->buffer[$session->getName()] = min($this->buffer[$session->getName()], $this->options("maxDist", 5.7));
                }
            } else {
                $this->buffer[$session->getName()] = max($this->buffer[$session->getName()] - 0.04, 0);
            }
            $session->setWaiting($this, false);
        }
    }
}