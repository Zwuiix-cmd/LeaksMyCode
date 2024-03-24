<?php

namespace AdvancedPrivateChest\Zwuiix\task;

use AdvancedPrivateChest\Zwuiix\Main;
use pocketmine\scheduler\Task;

class SavingData extends Task
{
    public function __construct()
    {
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, Main::getInstance()->getConfig()->getNested("task.period", 20));
    }

    public function onRun(): void
    {
        $main = Main::getInstance();
        $main->getLogger()->debug("Saving data.");
        $main->getData()->save();
    }
}