<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\Main;
use pocketmine\command\CommandSender;

class ACBlacklistListSubCommand extends BaseSubCommand
{
    /**
     * @return void
     */
    protected function prepare(): void {}

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $msg = [LanguageHandler::getInstance()->translate("blacklist_list")];
        foreach (Main::getInstance()->getBlacklist()->get("sessions", []) as $session) {
            $msg[] = LanguageHandler::getInstance()->translate("blacklist_display", [$session]);
        }
        $sender->sendMessage(implode("\n", $msg));
    }
}