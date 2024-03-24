<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use JsonException;
use MusuiAntiCheat\Zwuiix\command\arguments\ModuleArgument;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use pocketmine\command\CommandSender;

class ModuleEnableSubCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new ModuleArgument("module", false));
    }

    /**
     * @throws JsonException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $module = ModuleManager::getInstance()->findModuleByName($args["module"]);
        if(!$module instanceof Module) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("module_not_exist", [$args["module"]]));
            return;
        }

        if($module->isEnabled()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("module_already_enabled", [$args["module"]]));
           return;
        }

        $sender->sendMessage(LanguageHandler::getInstance()->translate("module_set_enabled", [$args["module"]]));
        $module->setEnabled(true);
    }
}