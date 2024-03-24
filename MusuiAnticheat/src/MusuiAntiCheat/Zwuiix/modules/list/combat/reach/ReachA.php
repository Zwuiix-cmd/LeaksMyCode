<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\reach;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\AABB;
use MusuiAntiCheat\Zwuiix\utils\Ray;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use ReflectionException;

class ReachA extends Module
{
    public function __construct()
    {
        parent::__construct("Reach", "A",
            ModuleManager::generateDefaultData(
                "Allows you to check whether someone is tapping a person outside the possible attack zone.",
                -1,
                ["maxDist" => 3.5, "maxBuffer" => 5]
            ));
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        // Pu ça mère
        if($packet instanceof InventoryTransactionPacket) {
            if($packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getTypeId() === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY && $session->getPlayer()->isSurvival()) {
                if($packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK) {
                    $session->setWaiting($this, true);
                }
            }
        }elseif($packet instanceof PlayerAuthInputPacket && $session->isWaiting($this)) {
            if ($session->getUserInfo()->getPlayerPlatform() !== "Android" && $session->attackPos !== null) {
                $ray = new Ray($session->attackPos, $session->getPlayer()->getDirectionVector());
                $AABB = AABB::fromPosition($session->lastLocation, 0.7001, 2.0001);
                $intersection = $AABB->calculateIntercept($ray->origin, $ray->traverse(7));
                if ($intersection !== null && !$AABB->toAABB()->isVectorInside($session->attackPos) && !$AABB->toAABB()->intersectsWith($session->getPlayer()->boundingBox->expandedCopy(0.5, -0.01, 0.5))) {
                    $raycastDist = $intersection->getHitVector()->distance($session->attackPos);
                    if(!isset($this->buffer[$session->getName()])) $this->buffer[$session->getName()] = 0;
                    if ($raycastDist > $this->options("maxDist", 3.5)) {
                        if (++$this->buffer[$session->getName()] >= $this->options("maxBuffer", 5)) {
                            $session->flag($this, ["type=raycast", "dist=" . round($raycastDist, 3), "maxDist={$this->options("maxDist", 3.5)}", "ping={$session->getNetwork()->getPing()}ms"]);
                            $this->buffer[$session->getName()] = min($this->buffer[$session->getName()], $this->options("maxBuffer", 3));
                        }
                    } else $this->buffer[$session->getName()] = max($this->buffer[$session->getName()] - 0.04, 0);
                }
            }
        }
    }
}