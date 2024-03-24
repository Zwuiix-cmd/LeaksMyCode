<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\killaura;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use ReflectionException;

class KillAuraA extends Module
{
    public function __construct()
    {
        parent::__construct("KillAura", "A", ModuleManager::generateDefaultData(
            "Check whether someone is automatically typing the people within the maximum distance zone that a basic client can type.",
            2,
            ["tickDiff" => 4]
        ));
    }

    /**
     * @param Session $session
     * @param mixed $packet
     * @param mixed|null $event
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        // TODO: https://github.com/ethaniccc/Esoteric/blob/master/src/ethaniccc/Esoteric/check/combat/killaura/KillAuraA.php
        if ($packet instanceof AnimatePacket && $packet->action === AnimatePacket::ACTION_SWING_ARM) {
            $session->lastTick = $session->currentTick;
        } elseif ($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK) {
            $tickDiff = $session->currentTick - $session->lastTick;
            if ($tickDiff > $this->options("tickDiff", 4)) {
                $session->flag($this, ["type=automatic", "tickDiff={$tickDiff}", "currentTick={$session->currentTick}", "lastTick={$session->lastTick}", "ping={$session->getNetwork()->getPing()}ms"]);
            }
        }
    }
}