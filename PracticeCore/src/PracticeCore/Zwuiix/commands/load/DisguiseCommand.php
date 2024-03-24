<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\RawStringArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TargetPlayerArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TextArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class DisguiseCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "disguise", "Disguise yourself as another player.", ["nick"]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.disguise");
        $this->registerArgument(0, new RawStringArgument("name", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        $disguiseName = $args["name"];
        $session = Server::getInstance()->getPlayerByPrefix($disguiseName);
        if($session instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("already_connected"));
            return;
        }

        if(!Session::isValidUserName($disguiseName)) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("disguise_error_username"));
            return;
        }

        $sender->setDisguise($disguiseName);
        $sender->getInfo()->update();
        $sender->sendMessage(LanguageHandler::getInstance()->translate("disguise_success", [$disguiseName]));
    }
}