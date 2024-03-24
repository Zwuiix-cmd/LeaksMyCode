<?php

namespace Zwuiix\Commands;

use pocketmine\command\CommandSender;
use Zwuiix\Libs\CortexPE\Commando\BaseCommand;
use Zwuiix\Player\User;

class Rekit extends BaseCommand
{

    protected function prepare(): void
    {
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof User)return;
        $sender->kit();
        $sender->sendMessage("§aVous avez bien été rekit!");
    }
}