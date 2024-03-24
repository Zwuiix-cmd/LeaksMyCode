<?php

namespace Zwuiix\AdvancedLightning\Trait;

use Zwuiix\AdvancedLightning\Listener\EventListener;
use Zwuiix\AdvancedLightning\Main;

trait LoaderTrait
{
    use DataTrait;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->saveDefaultConfig();
        $this->initData();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }
}