<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\PlayerArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AntiCheatPlayerInfoCommand extends BaseSubCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new PlayerArgument("session"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $player = Server::getInstance()->getPlayerByPrefix($args["session"]);
        if(!$player instanceof Player) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("not_connected", [$args["session"]]));
            return;
        }

        $session = SessionManager::getInstance()->getSession($player);
        if($session->isBlacklist()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("session_blacklisted", [$player->getName()]));
            return;
        }

        $msg = [LanguageHandler::getInstance()->translate("logs_title", [$player->getName()])];
        foreach ($session->logs as $name => $log) {
            $module = ModuleManager::getInstance()->findModuleByName($name);
            if(is_null($module)) break;
            $v = count($log);
            $c = round($v * 2 / $module->getMaxVL(), 3);

            $power = match (intval($c)) {
              0 => "Low",
              1 => "Medium",
              2 => "High",
            };

            $msg[]=LanguageHandler::getInstance()->translate("logs_display", [$name, $c, $power]);
        }

        $sender->sendMessage(implode("\n", $msg));
    }
}