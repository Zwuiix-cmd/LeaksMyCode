<?php

namespace MusuiScanner\Zwuiix;

use MusuiScanner;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use virion\CortexPE\Commando\exception\HookAlreadyRegistered;
use virion\CortexPE\Commando\PacketHooker;

class Plugin
{
    use SingletonTrait;

    protected MusuiScanner $musuiScanner;

    /**
     * @param MusuiScanner $musuiScanner
     */
    public function __construct(MusuiScanner $musuiScanner)
    {
        $this->musuiScanner = $musuiScanner;
        self::setInstance($this);
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     */
    public function enable(): void
    {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this->musuiScanner);
        }
        Server::getInstance()->getCommandMap()->register("musuiscanner", new MusuiScanner\Zwuiix\commands\ScannerCommand());
    }

    /**
     * @return MusuiScanner
     */
    public function getLoader(): MusuiScanner
    {
        return $this->musuiScanner;
    }
}