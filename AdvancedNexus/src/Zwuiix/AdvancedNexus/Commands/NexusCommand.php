<?php

namespace Zwuiix\AdvancedNexus\Commands;

use pocketmine\command\CommandSender;
use Zwuiix\AdvancedNexus\Commands\sub\NexusStartCommand;
use Zwuiix\AdvancedNexus\Commands\sub\NexusStopCommand;
use Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\BaseCommand;

class NexusCommand extends BaseCommand
{
    protected function prepare(): void
    {
        $this->registerSubCommand(new NexusStartCommand("start"));
        $this->registerSubCommand(new NexusStopCommand("stop"));
        $this->setPermission("advancednexus.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $this->sendUsage();
    }
}
