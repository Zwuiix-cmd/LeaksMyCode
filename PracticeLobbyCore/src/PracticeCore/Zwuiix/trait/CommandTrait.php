<?php

namespace PracticeCore\Zwuiix\trait;

use pocketmine\Server;
use PracticeCore\Zwuiix\commands\load\DisguiseCommand;
use PracticeCore\Zwuiix\commands\load\KnockbackCommand;
use PracticeCore\Zwuiix\commands\load\PingCommand;
use PracticeCore\Zwuiix\commands\load\RankCommand;
use PracticeCore\Zwuiix\commands\load\RekitCommand;
use PracticeCore\Zwuiix\commands\load\ReplyCommand;
use PracticeCore\Zwuiix\commands\load\SetAttackCooldownCommand;
use PracticeCore\Zwuiix\commands\load\SetHeightLimiterCommand;
use PracticeCore\Zwuiix\commands\load\SetKnockbackCommand;
use PracticeCore\Zwuiix\commands\load\SetRankCommand;
use PracticeCore\Zwuiix\commands\load\SpawnCommand;
use PracticeCore\Zwuiix\commands\load\StatsCommand;
use PracticeCore\Zwuiix\commands\load\TellCommand;
use PracticeCore\Zwuiix\commands\load\UndisguiseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;

trait CommandTrait
{
    /**
     * @return void
     */
    public function loadCommand(): void
    {
        foreach (Server::getInstance()->getCommandMap()->getCommands() as $command) {
            Server::getInstance()->getCommandMap()->unregister($command);
        }
    }
}