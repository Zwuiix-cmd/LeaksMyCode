<?php

namespace Zwuiix\AdvancedFreeze\Trait;

use pocketmine\utils\Config;

trait DataTrait
{
    private Config $config;

    public function initData(): void
    {
        $this->config=new Config($this->getDataFolder()."config.yml", Config::YAML);
    }

    /**
     * @return Config
     */
    public function getData(): Config
    {
        return $this->config;
    }
}