<?php

namespace MusuiAntiCheat\Zwuiix\command\load;

use MusuiAntiCheat\Zwuiix\command\subcommand\ACBlacklistAddSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\ACBlacklistListSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\ACBlacklistRemoveSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatAliasSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatBanSubCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatLogsCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatModulesCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatPlayerInfoCommand;
use MusuiAntiCheat\Zwuiix\command\subcommand\AntiCheatUnbanSubCommand;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use MusuiAntiCheat\Zwuiix\Main;
use pocketmine\command\CommandSender;

class AntiCheatCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Main::getInstance(), "anticheat", "Access to the anticheat control!", ["ac", "musui:anticheat", "anticheat:ac"]);
    }

    protected function prepare(): void
    {
        $this->setPermission("musuianticheat.anticheat.manage");
        $this->registerSubCommand(new AntiCheatPlayerInfoCommand("info"));
        $this->registerSubCommand(new AntiCheatLogsCommand("logs"));
        $this->registerSubCommand(new AntiCheatModulesCommand("modules"));
        $this->registerSubCommand(new AntiCheatBanSubCommand("ban"));
        $this->registerSubCommand(new AntiCheatUnbanSubCommand("unban"));
        $this->registerSubCommand(new AntiCheatAliasSubCommand("alias"));

        $this->registerSubCommand(new ACBlacklistAddSubCommand("bladd"));
        $this->registerSubCommand(new ACBlacklistRemoveSubCommand("blremove"));
        $this->registerSubCommand(new ACBlacklistListSubCommand("bllist"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $msg = [LanguageHandler::getInstance()->translate("help", [$this->getName()])];
        foreach ($this->getSubCommands() as $subCommand) {
            $msg[] = LanguageHandler::getInstance()->translate("help_display", [$subCommand->getName(), ($subCommand->getDescription() !== "" ? $subCommand->getDescription() : "Unknown")]);
        }

        $sender->sendMessage(implode("\n", $msg));
    }
}