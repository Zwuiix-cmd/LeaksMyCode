<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\AsyncForeachResult;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use ReflectionException;

class PacketsN extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "N",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
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
        if($packet instanceof InventoryTransactionPacket) {
            $trData = $packet->trData;
            if($trData instanceof UseItemTransactionData || $trData instanceof UseItemOnEntityTransactionData) {
                if(abs($trData->getHotbarSlot()) !== $trData->getHotbarSlot()) {
                    $session->flag($this, ["slot={$trData->getHotbarSlot()}"]);
                    return;
                }
            }
            Main::getInstance()->asyncIterator->forEach(new \ArrayIterator($trData->getActions()))->as(function (int $key, NetworkInventoryAction $action) use($session) {
                if(abs($action->inventorySlot) !== $action->inventorySlot) {
                    $session->flag($this, ["slot={$action->inventorySlot}"]);
                    return AsyncForeachResult::INTERRUPT();
                }
                return AsyncForeachResult::CONTINUE();
            });
        }
    }
}