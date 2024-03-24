<?php

namespace Zwuiix\Commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Zwuiix\Config\Message;
use Zwuiix\Libs\CortexPE\Commando\args\TargetArgument;
use Zwuiix\Libs\CortexPE\Commando\args\TextArgument;
use Zwuiix\Libs\CortexPE\Commando\BaseCommand;

class Tell extends BaseCommand
{

    protected function prepare(): void
    {
        $this->registerArgument(0, new TargetArgument());
        $this->registerArgument(1, new TextArgument("message"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player)return;
        $player=Server::getInstance()->getPlayerByPrefix($args["player"]);
        if(!$player instanceof Player) {
            $sender->sendMessage("§cDésolé, le joueur n'est pas connecté!");
            return;
        }
        if(count($args) < 2) {$this->sendUsage();return;}
        $message=implode(" ", $args);
        $sender->sendMessage("§7(Moi => {$player->getName()}) $message");
        $player->sendMessage("§7({$sender->getName()} => Moi) $message");
    }
}