<?php

namespace Zwuiix\AdvancedAntiUsebug\Trait;

use Zwuiix\AdvancedAntiUsebug\Listener\EventListener;

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
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this->getData()), $this);
    }
}