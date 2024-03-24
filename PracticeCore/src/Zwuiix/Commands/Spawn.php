<?php

namespace Zwuiix\Commands;

use pocketmine\command\CommandSender;
use Zwuiix\Libs\CortexPE\Commando\BaseCommand;
use Zwuiix\Player\User;
use Zwuiix\Tasks\TeleportTask;

class Spawn extends BaseCommand
{

    protected function prepare(): void
    {
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof User)return;
        $sender->spawn();
        $sender->sendMessage("§aVous avez bien été téléporter au spawn!");
    }
}