<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class PacketsA extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "A",
        ModuleManager::generateDefaultData(
            "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
            1,
            ["diff" => 2]
        ));
    }

    private array $ticks = [];

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
        if($packet instanceof PlayerAuthInputPacket) {
            if(!isset($this->ticks[$session->getName()])) {
                $this->ticks[$session->getName()] = $packet->getTick();
            }

            $this->ticks[$session->getName()]++;
            if($packet->getTick() !== $this->ticks[$session->getName()]) {
                $lastTickpp = $session->currentTick + 1;
                $diff = abs($packet->getTick() - $this->ticks[$session->getName()]);
                if($session->getPlayer()->isAlive() && $session->getPlayer()->getHealth() <= 1 && ($lastTickpp !== $packet->getTick() && $diff >= $this->options("diff", 2))) {
                    $session->flag($this, ["type=fake_tick", "diff={$diff}", "ping={$session->getNetwork()->getPing()}ms"]);
                }else $this->ticks[$session->getName()] = $packet->getTick();
            }
        }
    }
}