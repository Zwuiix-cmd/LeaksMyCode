<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\event\PacketReceiveAsyncEvent;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\block\tile\Sign;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use ReflectionException;

class PacketsB extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "B",
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
        if($packet instanceof BlockActorDataPacket && $event instanceof DataPacketReceiveEvent) {
            $nbt = $packet->nbt->getRoot();
            $tag = $nbt->getTag(Sign::TAG_FRONT_TEXT);
            if(!$tag instanceof CompoundTag) {
                $session->flag($this, ["type=block_actor_data", "ping={$session->getNetwork()->getPing()}ms"]);
                $event->cancel();
            }
        }
    }
}