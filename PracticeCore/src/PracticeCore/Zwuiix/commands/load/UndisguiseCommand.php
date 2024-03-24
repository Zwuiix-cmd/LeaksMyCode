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

class UndisguiseCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "undisguise", "Disguise yourself!", ["unnick"]);
    }

    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.undisguise");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        if(!$sender->isDisguise()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("not_disguise"));
            return;
        }

        $sender->setDisguise(null);
        $sender->getInfo()->update();
        $sender->sendMessage(LanguageHandler::getInstance()->translate("undisguise_success"));
    }
}