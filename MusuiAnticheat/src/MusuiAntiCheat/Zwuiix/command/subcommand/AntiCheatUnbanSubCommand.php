<?php

namespace MusuiAntiCheat\Zwuiix\command\subcommand;

use JsonException;
use MusuiAntiCheat\Zwuiix\handler\BanHandler;
use MusuiAntiCheat\Zwuiix\handler\LanguageHandler;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\RawStringArgument;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\BaseSubCommand;
use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AntiCheatUnbanSubCommand extends BaseSubCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
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
        $player = strtolower($args["name"]);
        if(Server::getInstance()->getPlayerByPrefix($args["name"]) instanceof Player) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("connect", [$args["name"]]));
            return;
        }

        if(!BanHandler::getInstance()->isBanned($player)) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("not_banned", [$args["name"]]));
            return;
        }

        BanHandler::getInstance()->unban($player);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("success_unbanned", [$player]));
    }
}