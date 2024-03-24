<?php

namespace Zwuiix\AdvancedFreeze\Handler;

use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AdvancedFreeze\Main;

class FreezeHandler
{
    use SingletonTrait;

    /**
     * @var string[]
     */
    protected array $players = array();

    public function __construct()
    {
        self::setInstance($this);
    }

    public function isFrozen(Player $player): bool
    {
        return isset($this->players[$player->getName()]);
    }

    public function setFrozen(Player $player, bool $resp = true): void
    {
        $packet = new LevelEventPacket();
        $packet->eventId = 3005;
        $packet->eventData = ($resp ? 1 : 0);
        $player->getNetworkSession()->sendDataPacket($packet);
        $player->setImmobile($resp);


        if($resp) {
            $player->sendMessage(Main::getInstance()->getData()->getNested("messages.frozen"));
            $this->players[$player->getName()]=$player;
        }else{
            if($this->isFrozen($player)) unset($this->players[$player->getName()]);
        }
    }
}