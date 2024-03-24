<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class SpawnCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "spawn", "Teleports to the spawn!");
    }

    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.spawn");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        $sender->spawn();
        $sender->sendMessage(LanguageHandler::getInstance()->translate("teleport_to_spawn"));
    }
}