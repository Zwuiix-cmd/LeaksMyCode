<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use pocketmine\command\CommandSender;

class AntiCheatModulesCommand extends BaseSubCommand
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
        $msg = [LanguageHandler::getInstance()->translate("module_result")];
        foreach (ModuleManager::getInstance()->getAll() as $module) {
            $msg[] = LanguageHandler::getInstance()->translate("module_display", [$module->getName(), $module->getType()]);
        }

        $sender->sendMessage(implode("\n", $msg));
    }
}