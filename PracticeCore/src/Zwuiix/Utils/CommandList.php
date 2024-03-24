<?php

namespace Zwuiix\Utils;

use Zwuiix\Commands\Logs;
use Zwuiix\Commands\Ping;
use Zwuiix\Commands\Rekit;
use Zwuiix\Commands\Spawn;
use Zwuiix\Commands\Stats;
use Zwuiix\Commands\Tell;
use Zwuiix\Main;

class CommandList
{

    public static function getListCommand(Main $main): array
    {
        return [
            new Ping($main, "ping", "Obtenir le ping d'un joueur ou le sien", ["latency"]),
            new Tell($main, "tell", "Envoyer un message privé!", ["w", "m", "msg", "whipser"]),
            new Spawn($main, "spawn", "Allez au spawn!"),
            new Rekit($main, "rekit", "Permet de se rekit!"),
            new Stats($main, "stats", "Permet de voir les statistiques d'un joueur!"),
            new Logs($main, "logs", "Permet de gérer ces logs."),
        ];
    }

    public static function getListUnregisterCommand(): array
    {
        return [
            "defaultgamemode",
            "checkperm",
            "list",
            "gc",
            "say",
            "version",
            "kill",
            "particle",
            "seed",
            "tell",
            "spawnpoint",
            "save-off",
            "save-on",
            "save-all",
            "plugins",
            "?",
            "me",
            "kick",
            "ban",
            "unban",
            "clear"
        ];
    }
}