<?php

namespace PlutooCore\task;

use PlutooCore\handlers\event\EventHandler;
use pocketmine\scheduler\Task;

class EventTask extends Task
{
    public function __construct()
    {
        \PlutooCore::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function onRun(): void
    {
        foreach (EventHandler::getInstance()->getAll() as $item) {
            $date = date("H:i:s");
            if(in_array($date, $item->getTimes())) {
                $item->call();
            }
        }
    }
}
