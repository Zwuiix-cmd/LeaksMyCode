<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\AsyncForeachResult;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use ReflectionException;

class PacketsK extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "K",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
                ["enchants" => 100, "lores" => 100, "canDestroy" => 100, "canPlace" => 10]
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
        if($packet instanceof MobArmorEquipmentPacket) {
            Main::getInstance()->asyncIterator->forEach(new \ArrayIterator([$packet->head, $packet->chest, $packet->legs, $packet->feet]))->as(function ($item) use($session) {
                $item = TypeConverter::getInstance()->netItemStackToCore($item->getItemStack());
                if (
                    ($enchants = count($item->getEnchantments())) >= $this->options("enchants", 100) ||
                    ($lore = count($item->getEnchantments())) >= $this->options("lores", 100) ||
                    ($canDestroy = count($item->getEnchantments())) >= $this->options("canDestroy", 100) ||
                    ($canPlace = count($item->getEnchantments())) >= $this->options("canPlace", 100)
                ) {
                    $session->flag($this, ["enchants={$enchants}", "lores={$lore}", "canDestroy={$canDestroy}", "canPlace={$canPlace}"]);
                    return AsyncForeachResult::INTERRUPT();
                }
                return AsyncForeachResult::CONTINUE();
            });
        }
    }
}