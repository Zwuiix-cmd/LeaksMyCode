<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\nuker;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use ReflectionException;

class NukerA extends Module
{
    public function __construct()
    {
        parent::__construct("Nuker", "A",
            ModuleManager::generateDefaultData(
                "It allows you to check whether the customer is breaking several blocks at once, or moving in completely different directions.",
                1,
                ["maxBlocksPerPacket", 20]
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
        if($packet instanceof PlayerAuthInputPacket) {
            $blocks = $packet->getBlockActions();
            if(!is_null($blocks)) {
                $last = count($session->lastBlockActions);
                $new = count($blocks);

                $diff = abs($last - $new);
                if($new > $last && $new >= $this->options("maxBlocksPerPacket", 20)) {
                    $session->flag($this, ["type=moment", "lastblocks={$last}", "newblocks={$new}", "diff={$diff}", "ping={$session->getNetwork()->getPing()}ms"], true);
                }

                $session->lastBlockActions = $blocks;
            }
        }
    }
}