<?php

namespace Zwuiix\Commands;

use pocketmine\command\CommandSender;
use Zwuiix\Config\Message;
use Zwuiix\Libs\CortexPE\Commando\args\BooleanArgument;
use Zwuiix\Libs\CortexPE\Commando\BaseCommand;
use Zwuiix\Player\User;
use Zwuiix\Utils\Permissions;
use Zwuiix\Utils\Utils;

class Logs extends BaseCommand
{
    protected function prepare(): void
    {
        $this->registerArgument(0, new BooleanArgument("type", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof User) {
            $sender->sendMessage("§cVeuillez faire cela depuis le jeu!");
            return;
        }

        $sender->isLogsMode=$args["type"] ?? !$sender->isLogsMode;
        $sender->sendMessage("§aVous avez bien §7" . Utils::boolToString($sender->isLogsMode) . "§a le mode logs!");
    }
}