<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use MusuiAntiCheat\Zwuiix\handler\AliasesHandler;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\PlayerArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AntiCheatAliasSubCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new PlayerArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $player = Server::getInstance()->getPlayerByPrefix($args["player"]);
        if(!$player instanceof Player) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("not_connected", [$args["player"]]));
            return;
        }

        $session = SessionManager::getInstance()->getSession($player);
        $alt = ($handler = AliasesHandler::getInstance())->getAlt($session);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("alias_result", [
            $player->getName(),
            $handler->altFormat(", ",$alt[$handler::ADDRESS]),
            $handler->altFormat(", ",$alt[$handler::UUID]),
            $handler->altFormat(", ",$alt[$handler::XUID]),
            $handler->altFormat(", ",$alt[$handler::DEVICEID]),
        ]));
    }
}