<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\timer;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\Server;

class TimerB extends Module
{
    private array $samples = [];

    public function __construct()
    {
        parent::__construct("Timer", "B",
            ModuleManager::generateDefaultData(
                "Allows you to check whether a player is playing faster or slower than usual.",
                10,
                ["max" => 40, "maxBuffer" => 5, "deviation" => 12]
            ));
    }

    /**
     * @param Session $session
     * @param mixed $packet
     * @param mixed|null $event
     * @return void
     * @throws JsonException
     * @throws \ReflectionException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof PlayerAuthInputPacket){
            // Aucun risque celui-ci reste privé
        }
    }
}