<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TargetPlayerArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class PingCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "ping", "View the pings of a player or yourself.", ["ms", "latency"]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.ping");
        $this->registerArgument(0, new TargetPlayerArgument(true, "name"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        if(isset($args["name"])) {
            $session = Server::getInstance()->getPlayerByPrefix($args["name"]);
            if(!$session instanceof Session) {
                $sender->sendMessage(LanguageHandler::getInstance()->translate("player_not_online"));
                return;
            }

            $sender->sendMessage(LanguageHandler::getInstance()->translate("player_s_ping", [$session->getName(), $session->getNetworkSession()->getPing()]));
            return;
        }

        $sender->sendMessage(LanguageHandler::getInstance()->translate("your_ping", [$sender->getNetworkSession()->getPing()]));
    }
}