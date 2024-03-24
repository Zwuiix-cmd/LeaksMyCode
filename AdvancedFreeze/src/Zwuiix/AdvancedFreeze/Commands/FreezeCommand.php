<?php

namespace Zwuiix\AdvancedFreeze\Commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Zwuiix\AdvancedFreeze\Handler\FreezeHandler;
use Zwuiix\AdvancedFreeze\Lib\CortexPE\Commando\args\BooleanArgument;
use Zwuiix\AdvancedFreeze\Lib\CortexPE\Commando\args\TargetPlayerArgument;
use Zwuiix\AdvancedFreeze\Lib\CortexPE\Commando\BaseCommand;
use Zwuiix\AdvancedFreeze\Main;
use AdvancedNexus\src\Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\exception\ArgumentOrderException;

class FreezeCommand extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TargetPlayerArgument());
        $this->registerArgument(1, new BooleanArgument("type", true));
        $this->setPermission("advancedfreeze.commands.freeze");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {

        var_dump(Server::getInstance()->getOfflinePlayerData("zwu11x"));

        $player=Server::getInstance()->getPlayerByPrefix($args["player"]);
        if(!$player instanceof Player){
            $sender->sendMessage(Main::getInstance()->getData()->getNested("messages.target-not-found"));
            return;
        }

        $res = $args["type"] ?? !FreezeHandler::getInstance()->isFrozen($player);
        FreezeHandler::getInstance()->setFrozen($player, $res);
        $sender->sendMessage(str_replace(["{PLAYER}", "{TYPE}"], [$player->getName(), $res], Main::getInstance()->getData()->getNested("messages.target-frozen")));
        return;
    }
}