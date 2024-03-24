<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\combat\autoclicker;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use ReflectionException;

class AutoClickerA extends Module
{
    public function __construct()
    {
        parent::__construct("AutoClicker", "A", ModuleManager::generateDefaultData(
            "Allows you to detect anyone who clicks really fast, who clicks automatically via software or a macro.",
            20,
            ["average" => 30, "unique" => 25]
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
        // Il marche plus depuis là 1.20 j'crois
        if($packet instanceof LevelSoundEventPacket) {
            if($packet::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $packet->sound === LevelSoundEvent::ATTACK_NODAMAGE or $packet->sound === LevelSoundEvent::ATTACK_STRONG){
                $this->check($session);
            }
        }
    }

    /**
     * @param Session $session
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    public function check(Session $session): void
    {
        $click = function (bool $average = false) use ($session) {
            return $session->getCpsManager()->getClick($average);
        };
        if(($b1 = $click(true) >= $this->options("average", 25)) || $click() >= $this->options("unique", 40)) {
            $session->flag($this, ["type=" . ($b1 ? "average" : "moment"), "moment={$click()}", "average={$click(true)}", "ping={$session->getNetwork()->getPing()}ms"]);
        }
    }
}