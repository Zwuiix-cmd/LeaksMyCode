<?php

namespace MusuiAntiCheat\Zwuiix\session;

use JsonException;
use MusuiAntiCheat\Zwuiix\handler\AliasesHandler;
use MusuiAntiCheat\Zwuiix\handler\BanHandler;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class SessionManager
{
    use SingletonTrait;

    /*** @var Session[] */
    protected array $sessions = array();

    /**
     * @param Player $player
     * @return Session
     */
    public function getSession(Player $player): Session
    {
        return $this->sessions["{$player->getName()}:{$player->getNetworkSession()->getPort()}"] ?? $this->sessions["{$player->getName()}:{$player->getNetworkSession()->getPort()}"] = new Session($player);
    }

    /**
     * @param string $name
     * @return Session|null
     */
    public function getSessionByName(string $name): ?Session
    {
        return $this->sessions[$name];
    }

    /**
     * @return Session[]
     */
    public function getAll(): array
    {
        return $this->sessions;
    }

    /**
     * @param Player $player
     * @return void
     * @throws JsonException
     */
    public function construct(Player $player): void
    {
        $this->sessions["{$player->getName()}:{$player->getNetworkSession()->getPort()}"] = new Session($player);
        $session = $this->getSession($player);
        $session->setConnected(true);
        BanHandler::getInstance()->canConnect($session);
        AliasesHandler::getInstance()->initializeSession($session);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function destruct(Player $player): void
    {
        if($this->sessions["{$player->getName()}:{$player->getNetworkSession()->getPort()}"]) {
            unset($this->sessions["{$player->getName()}:{$player->getNetworkSession()->getPort()}"]);
        }
    }
}