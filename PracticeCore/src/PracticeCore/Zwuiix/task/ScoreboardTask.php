<?php

namespace PracticeCore\Zwuiix\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use PracticeCore\Zwuiix\handler\ScoreboardHandler;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;
use PracticeCore\Zwuiix\utils\Data;

class ScoreboardTask extends Task
{
    protected Data $data;

    public function __construct()
    {
        PracticeCore::getInstance()->getPlugin()->getScheduler()->scheduleRepeatingTask($this, 1);
    }

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if(!$onlinePlayer instanceof Session) continue;
            if(!$onlinePlayer->getInfo()->hasScoreboard()) continue;
            $scoreboard = $onlinePlayer->getScoreboard();

            $combatCooldown = $onlinePlayer->getCooldownByName(Session::TAG_COMBAT_LOGGER_COOLDOWN);
            $enderPearlCooldown = $onlinePlayer->getCooldownByName(Session::TAG_ENDER_PEARL_COOLDOWN);

            $lines = [
                ScoreboardHandler::getInstance()->translate("first_line"),
                "§7",
            ];

            $enderPearl = false;
            if($combatCooldown->isInCooldown()) {
                if($enderPearlCooldown->isInCooldown()) {
                    $lines[] = ScoreboardHandler::getInstance()->translate("ender_pearl", [($enderPearlCooldown->getCooldown() / 20)]);
                }
                $lines[] = ScoreboardHandler::getInstance()->translate("combat_time", [($combatCooldown->getCooldown() / 20)]);
                $lines[] = "§7§b";

                if($onlinePlayer->hasOpponent() && $onlinePlayer->getOpponent()->isConnected()) {
                    $lines[] = ScoreboardHandler::getInstance()->translate("opponent", [$onlinePlayer->getOpponent()->getName()]);
                    $lines[] = "§7§c";
                    $lines[] = ScoreboardHandler::getInstance()->translate("your_ping", [$onlinePlayer->getNetworkSession()->getPing()]);
                    $lines[] = ScoreboardHandler::getInstance()->translate("their_ping", [$onlinePlayer->getOpponent()->getNetworkSession()->getPing()]);
                    $lines[] = "§7§d";
                }
            } else {
                $lines[1] = ScoreboardHandler::getInstance()->translate("online", [count(Server::getInstance()->getOnlinePlayers())]);
                $lines[] = "§7§e";
                $lines[] = ScoreboardHandler::getInstance()->translate("kill", [$onlinePlayer->getInfo()->getKill()]);
                $lines[] = ScoreboardHandler::getInstance()->translate("killstreak", [$onlinePlayer->getInfo()->getKillStreak()]);
                $lines[] = ScoreboardHandler::getInstance()->translate("death", [$onlinePlayer->getInfo()->getDeath()]);
            }

            $lines[] = "§7§m";
            $lines[] = ScoreboardHandler::getInstance()->translate("address");
            $lines[] = ScoreboardHandler::getInstance()->translate("second_line");

            $scoreboard->sendScoreboard($onlinePlayer, ScoreboardHandler::getInstance()->translate("name"), $lines);
        }
    }
}