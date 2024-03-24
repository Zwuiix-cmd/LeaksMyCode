<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\kit\NodebuffKit;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class RekitCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "rekit", "Allows you to rekit yourself!");
    }

    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.rekit");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        if($sender->getWorld()->getFolderName() !== "Nodebuff") {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("rekit_error_world"));
            return;
        }

        NodebuffKit::getInstance()->give($sender);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("rekit_success"));
    }
}