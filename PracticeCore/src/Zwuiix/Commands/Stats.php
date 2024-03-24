<?php

namespace Zwuiix\Commands;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zwuiix\Libs\CortexPE\Commando\args\TargetArgument;
use Zwuiix\Libs\CortexPE\Commando\BaseCommand;
use Zwuiix\Libs\CortexPE\Commando\exception\ArgumentOrderException;
use Zwuiix\Player\User;
use Zwuiix\Utils\Utils;

class Stats extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TargetArgument());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof User)return;
        $player=Server::getInstance()->getPlayerByPrefix($args["player"]);
        if(!$player instanceof User) {
            $sender->sendMessage("§cDésolé, le joueur n'est pas connecter!");
            return;
        }
        \Zwuiix\Interface\Stats::getInstance()->send($sender, $player);
    }
}