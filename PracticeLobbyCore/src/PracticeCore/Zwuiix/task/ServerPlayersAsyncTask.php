<?php

namespace PracticeCore\Zwuiix\task;

use pocketmine\scheduler\AsyncTask;
use PracticeCore\Zwuiix\handler\ServersHandler;
use PracticeCore\Zwuiix\libs\libpmquery\PMQuery;
use PracticeCore\Zwuiix\libs\libpmquery\PmQueryException;
use PracticeCore\Zwuiix\server\Server;
use PracticeCore\Zwuiix\server\ServerQueryResponse;

class ServerPlayersAsyncTask extends AsyncTask
{
    public function __construct(
        protected string $serverName,
        protected string $address,
        protected int $port,
    )
    {
    }

    public function onRun(): void
    {
        [$host, $port] = [$this->address, $this->port];
        $data = [];
        try {
            $data = PMQuery::query($host, $port);
        } catch (PmQueryException $exception) {
        }

        $this->setResult(new ServerQueryResponse($data));
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();
        if(!$result instanceof ServerQueryResponse) {
            return;
        }

        $server = ServersHandler::getInstance()->getServerByName($this->serverName);
        if(!$server instanceof Server) {
            return;
        }

        if(isset($result->getData()["Players"])) {
            $server->setPlayers(intval($result->getData()["Players"]));
        }
    }
}