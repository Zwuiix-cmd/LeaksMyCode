<?php

namespace MusuiAntiCheat\Zwuiix\modules;

use MusuiAntiCheat\Zwuiix\command\arguments\ModuleArgument;
use MusuiAntiCheat\Zwuiix\event\PacketReceiveAsyncEvent;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class ModuleManager
{
    use SingletonTrait;

    /*** @var Module[] */
    protected array $modules = array();

    public function __construct()
    {
        self::setInstance($this);
    }

    /**
     * @param Module $module
     * @return void
     */
    public function register(Module $module): void
    {
        $name = strtolower($module->getName() . $module->getType());
        if($this->exist($name)) {
            return;
        }

        if(in_array($name, Main::getInstance()->getData()->getNested("blacklist.module", []))) {
            return;
        }

        $this->modules[$name] = $module;
        ModuleArgument::$VALUES[$name] = "{$module->getName()}{$module->getType()}";
        Server::getInstance()->getLogger()->debug("[AntiCheat] | Module => {$module->getName()}{$module->getType()} has been loaded!");
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exist(string $name): bool
    {
        return isset($this->modules[strtolower($name)]);
    }

    /**
     * @return Module[]
     */
    public function getAll(): array
    {
        return $this->modules;
    }

    public function callInbound(Session $session, mixed $information, DataPacketReceiveEvent $event = null): void
    {
        if(!$session->getPlayer()->spawned) return;
        if(!$session->getPlayer()->isConnected()) return;
        if($session->isBlacklist()) return;

        foreach ($this->modules as $module) {
            if(!$module->isEnabled()) continue;

            $module->getTimings()->startTiming();
            $module->callInbound($session, $information, $event);
            $module->getTimings()->stopTiming();
            if($module->isDetected()) {
                $module->detect(false);
                return;
            }
        }
    }

    /**
     * @param Session $session
     * @param ClientboundPacket $packet
     * @param DataPacketSendEvent $event
     * @return void
     */
    public function callOutbound(Session $session, ClientboundPacket $packet, DataPacketSendEvent $event): void
    {
        if(!$session->getPlayer()->spawned) return;
        if(!$session->getPlayer()->isConnected()) return;
        if($session->isBlacklist()) return;

        foreach ($this->modules as $module) {
            if(!$module->isEnabled()) continue;

            $module->getTimings()->startTiming();
            $module->callOutbound($session, $packet, $event);
            $module->getTimings()->stopTiming();
            if($module->isDetected()) {
                $module->detect(false);
                return;
            }
        }
    }

    /**
     * @param string $description
     * @param int $maxVL
     * @param array $others
     * @return array
     */
    public static function generateDefaultData(string $description, int $maxVL, array $others = []): array
    {
        return [
            "enabled" => true,
            "description" => "$description",
            "maxVL" => $maxVL,
        ] + $others;
    }

    /**
     * @param string $name
     * @return Module|null
     */
    public function findModuleByName(string $name): ?Module
    {
        return $this->modules[strtolower($name)] ?? null;
    }
}