<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\bot;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\TickSyncPacket;
use ReflectionException;

class BotA extends Module
{
    public function __construct()
    {
        parent::__construct("Bot", "A",
            ModuleManager::generateDefaultData(
                "Make sure the customer isn't a robot!",
                3,
                ["maxPerSec" => 5]
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
        if($packet instanceof TickSyncPacket) {
            // Private
        }
    }
}