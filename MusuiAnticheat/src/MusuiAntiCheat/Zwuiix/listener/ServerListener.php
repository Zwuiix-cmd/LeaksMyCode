<?php

namespace MusuiAntiCheat\Zwuiix\listener;

use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\pmmp\MusuiRakLibInterface;
use MusuiAntiCheat\Zwuiix\utils\ReflectionUtils;
use pocketmine\event\Listener;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\query\DedicatedQueryNetworkInterface;
use pocketmine\Server;
use ReflectionException;

class ServerListener implements Listener
{
    protected array $queryPlugins = [];

    /**
     * @param NetworkInterfaceRegisterEvent $event
     * @return void
     * @throws ReflectionException
     */
    public function onInterfaceRegister(NetworkInterfaceRegisterEvent $event): void
    {
        // Flemme de l'update
        /*$interface = $event->getInterface();
        if($interface instanceof DedicatedQueryNetworkInterface) {
            $event->cancel();
        }elseif (!$interface instanceof MusuiRakLibInterface && $interface instanceof RakLibInterface) {
            $event->cancel();
            $server = Server::getInstance();
            $newInterface = new MusuiRakLibInterface($server, $server->getIp(), $server->getPort(), false,
                ReflectionUtils::getProperty(RakLibInterface::class, $interface, "packetBroadcaster"),
                ReflectionUtils::getProperty(RakLibInterface::class, $interface, "entityEventBroadcaster"),
                ReflectionUtils::getProperty(RakLibInterface::class, $interface, "packetSerializerContext"),
                ReflectionUtils::getProperty(RakLibInterface::class, $interface, "typeConverter"),
            );
            $server->getNetwork()->registerInterface($newInterface);
        }*/
    }

    /**
     * @param QueryRegenerateEvent $event
     * @return void
     */
    public function onQueryRegenerate(QueryRegenerateEvent $event): void
    {
        $info = $event->getQueryInfo();

        if(!isset($info->getPlugins()["MusuiAntiCheat"])) {
          $info->setPlugins(["MusuiAntiCheat" => Main::getInstance()]);
        }

        $info->setPlugins($this->queryPlugins);
        $info->setServerName("{$info->getServerName()} with MusuiAntiCheat");
    }
}