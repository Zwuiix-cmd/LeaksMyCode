<?php

namespace MusuiAntiCheat\Zwuiix\command\load;

use MusuiAntiCheat\Zwuiix\command\subcommand\ModuleDisableSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\ModuleEnableSubCommand;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use MusuiAntiCheat\Zwuiix\Main;
use pocketmine\command\CommandSender;

class ACModuleCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Main::getInstance(), "acmodule", "Access to the anticheat module control!", ["musui:anticheatmodule", "anticheat:acmodule"]);
    }


    protected function prepare(): void
    {
        $this->setPermission("musuianticheat.anticheat.module");
        $this->registerSubCommand(new ModuleEnableSubCommand("enable"));
        $this->registerSubCommand(new ModuleDisableSubCommand("disable"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $msg = [LanguageHandler::getInstance()->translate("acblacklist", [$this->getName()])];
        foreach ($this->getSubCommands() as $subCommand) {
            $msg[] = LanguageHandler::getInstance()->translate("help_display", [$subCommand->getName(), ($subCommand->getDescription() !== "" ? $subCommand->getDescription() : "Unknown")]);
        }

        $sender->sendMessage(implode("\n", $msg));
    }
}