<?php

namespace Zwuiix\AdvancedPurification\Trait;

use Zwuiix\AdvancedPurification\Task\PurificationTask;

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
        $this->getScheduler()->scheduleRepeatingTask(new PurificationTask($this, $this->getData()), 20);
    }
}