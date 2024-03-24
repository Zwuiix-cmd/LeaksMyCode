<?php

namespace PracticeCore\Zwuiix\trait;

use JsonException;
use PracticeCore\Zwuiix\handler\ServersHandler;
use PracticeCore\Zwuiix\server\Server;
use PracticeCore\Zwuiix\utils\PathScanner;
use Symfony\Component\Filesystem\Path;

trait ServersTrait
{
    /**
     * @throws JsonException
     */
    public function loadServers(): void
    {
        $servers = PathScanner::scanDirectoryToData(Path::join($this->getPlugin()->getDataFolder() . "/servers/"), ["yml"]);
        foreach ($servers as $server) {
            ServersHandler::getInstance()->register(new Server(
                $server->get("name", "Practice"),
                $server->get("address", "colria.club"),
                intval($server->get("port", 19132)),
            ));
        }
    }
}