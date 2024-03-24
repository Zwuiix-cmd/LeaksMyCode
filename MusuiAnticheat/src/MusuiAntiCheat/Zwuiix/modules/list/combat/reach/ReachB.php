<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\reach;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\AABB;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\Server;
use ReflectionException;

class ReachB extends Module
{
    public function __construct()
    {
        parent::__construct("Reach", "B",
            ModuleManager::generateDefaultData(
                "Allows you to check whether someone is tapping a person outside the possible attack zone.",
                -1,
                ["lastTeleport" => 10, "max" => 6]
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
                    if ($session->teleportTicks <= $this->options("lastTeleport", 10)) {
                        return;
                    }
                    $target = Server::getInstance()->getWorldManager()->findEntity($packet->trData->getActorRuntimeId());
                    if(!$target instanceof Entity) return;

                    $dist = AABB::getEyePosition($packet->trData->getPlayerPosition(), $session->getPlayer()->getEyeHeight())->distance($target->getEyePos());
                    if($dist > $this->options("max", 6) && $session->teleportTicks >= 15) {
                        $session->flag($this, ["type=aabb", "dist={$dist}", "ping={$session->getNetwork()->getPing()}ms"]);
                    }
                }
            }
        }
    }
}