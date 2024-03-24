<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\AsyncForeachResult;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\item\Item;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ClientCacheBlobStatusPacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\EmoteListPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\PurchaseReceiptPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\SubChunkRequestPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use ReflectionException;

class PacketsM extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "M",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
                [
                    "maxArray" => 100
                ]
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
            // Aucun risque celui ci
        }
    }
}