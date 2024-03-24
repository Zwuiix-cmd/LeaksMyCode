<?php

namespace Zwuiix\Libs\SenseiTarzan\Task;

use pocketmine\scheduler\Task;
use Zwuiix\Libs\SenseiTarzan\Compement\ResourcePackManager;

class ResourcePackSendDataPacket extends Task
{
    private float $nextTick = 2;
    /**
     * @inheritDoc
     */
    public function onRun(): void
    {
        $this->nextTick--;
        if ($this->nextTick <= 0) {
            $this->nextTick = 2;
            foreach (ResourcePackManager::$packSend as $packet) {
                $packet->tick();
            }
        }
    }
}