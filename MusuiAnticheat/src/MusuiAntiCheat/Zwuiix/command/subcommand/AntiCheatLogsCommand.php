<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\BooleanArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AntiCheatLogsCommand extends BaseSubCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new BooleanArgument("status"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player) {
            return;
        }

        $session = SessionManager::getInstance()->getSession($sender);
        $session->setLogs($args["status"]);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("logs_change_display", [($args ? LanguageHandler::getInstance()->translate("logs_change_enable") : LanguageHandler::getInstance()->translate("logs_change_disable"))]));
    }
}