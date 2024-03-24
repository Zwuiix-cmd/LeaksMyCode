<?php


use pocketmine\plugin\PluginBase;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\HookAlreadyRegistered;
use PracticeCore\Zwuiix\MultiProtocol;
use PracticeCore\Zwuiix\PracticeCore;

class Loader extends PluginBase
{
    protected PracticeCore $practiceCore;
    protected MultiProtocol $multiProtocol;

    /**
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    protected function onLoad(): void
    {
        $this->practiceCore = new PracticeCore($this);;
        $this->multiProtocol = new MultiProtocol($this);;
    }

    /**
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    protected function onEnable(): void
    {
        $this->practiceCore->enable();
        $this->multiProtocol->enable();
    }

    /**
     * @throws JsonException
     */
    protected function onDisable(): void
    {
        $this->getPracticeCore()->__destruct();
        $this->getMultiProtocol()->__destruct();
    }

    /**
     * @return PracticeCore
     */
    public function getPracticeCore(): PracticeCore
    {
        return $this->practiceCore;
    }

    /**
     * @return MultiProtocol
     */
    public function getMultiProtocol(): MultiProtocol
    {
        return $this->multiProtocol;
    }
}