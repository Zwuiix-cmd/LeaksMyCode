<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use JsonException;
use MusuiAntiCheat\Zwuiix\handler\BanHandler;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\PlayerArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\TextArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AntiCheatBanSubCommand extends BaseSubCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new PlayerArgument("session"));
        $this->registerArgument(1, new TextArgument("reason"));
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     * @throws JsonException
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

        $i = 0;
        foreach ($session->logs as $log) {
            $i += count($log);
        }

        BanHandler::getInstance()->ban($session, "{$sender->getName()}", $args["reason"], "totalLogs={$i}");
        $sender->sendMessage(LanguageHandler::getInstance()->translate("success_banned", [$player->getName()]));
    }
}