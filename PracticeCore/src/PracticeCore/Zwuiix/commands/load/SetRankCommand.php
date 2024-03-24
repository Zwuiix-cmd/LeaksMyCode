<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use PracticeCore\Zwuiix\commands\arguments\RankArgument;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\handler\RankHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TargetPlayerArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\rank\Rank;
use PracticeCore\Zwuiix\session\Session;

class SetRankCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "setrank", "Defines a player's rank.");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.setrank");
        $this->registerArgument(0, new TargetPlayerArgument(false));
        $this->registerArgument(1, new RankArgument("rank"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $session = Server::getInstance()->getPlayerExact($args["player"]);
        if(!$session instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("player_not_online"));
            return;
        }

        $rank = RankHandler::getInstance()->getRankByName($args["rank"]);
        if(!$rank instanceof Rank) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("rank_not_exist"));
            return;
        }

        $session->getInfo()->setRank($rank);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("set_rank_success", [$session->getName(), $rank->getName()]));
    }
}