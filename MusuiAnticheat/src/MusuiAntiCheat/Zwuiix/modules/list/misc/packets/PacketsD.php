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
use ReflectionException;

class PacketsD extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "D",
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
        if ($packet instanceof InventoryTransactionPacket) {
            Main::getInstance()->asyncIterator->forEach(new \ArrayIterator($packet->trData->getActions()))->as(function ($key, $action) use($session) {
                if($action->sourceType === NetworkInventoryAction::SOURCE_CREATIVE && !$session->getPlayer()->isCreative() && $session->getPlayer()->isConnected()){
                    $session->flag($this, ["type=fake_creative", "ping={$session->getNetwork()->getPing()}ms"]);
                    return AsyncForeachResult::INTERRUPT();
                }
                return AsyncForeachResult::CONTINUE();
            });
        }
    }
}