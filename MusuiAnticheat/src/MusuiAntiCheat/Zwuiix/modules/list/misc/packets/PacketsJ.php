<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use ReflectionException;

class PacketsJ extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "J",
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
        if($packet instanceof MobEquipmentPacket) {
            $item = TypeConverter::getInstance()->netItemStackToCore($packet->item->getItemStack());
            if(
                ($canDestroy = count($item->getEnchantments())) >= $this->options("canDestroy", 100) ||
                ($canPlace = count($item->getEnchantments())) >= $this->options("canPlace", 100)
            ) {
                $session->flag($this, ["canDestroy={$canDestroy}", "canPlace={$canPlace}"]);
            }
        }
    }
}