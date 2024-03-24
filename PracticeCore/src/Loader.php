<?php


use pocketmine\plugin\PluginBase;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\HookAlreadyRegistered;
use PracticeCore\Zwuiix\PracticeCore;

class Loader extends PluginBase
{
    protected PracticeCore $practiceCore;

    /**
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        $this->practiceCore = new PracticeCore($this);
    }

    /**
     * @throws JsonException
     */
    protected function onDisable(): void
    {
        $this->getPracticeCore()->__destruct();
    }

    /**
     * @return PracticeCore
     */
    public function getPracticeCore(): PracticeCore
    {
        return $this->practiceCore;
    }
}