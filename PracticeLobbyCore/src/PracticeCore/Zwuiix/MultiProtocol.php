<?php

namespace PracticeCore\Zwuiix;

use JsonException;
use pocketmine\event\EventPriority;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PacketViolationWarningPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\query\DedicatedQueryNetworkInterface;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\HookAlreadyRegistered;
use PracticeCore\Zwuiix\libs\muqsit\simplepackethandler\SimplePacketHandler;
use PracticeCore\Zwuiix\network\ColriaNetworkSession;
use PracticeCore\Zwuiix\network\ColriaRakLibInterface;
use PracticeCore\Zwuiix\network\ColriaRakNetProtocolAcceptor;
use PracticeCore\Zwuiix\network\proto\latest\LatestProtocol;
use PracticeCore\Zwuiix\network\proto\v361\v361PacketTranslator;
use PracticeCore\Zwuiix\network\proto\v486\v486PacketTranslator;
use Symfony\Component\Filesystem\Path;

class MultiProtocol
{
    use SingletonTrait;

    private const PACKET_VIOLATION_WARNING_TYPE = [
        PacketViolationWarningPacket::TYPE_MALFORMED => "MALFORMED",
    ];
    private const PACKET_VIOLATION_WARNING_SEVERITY = [
        PacketViolationWarningPacket::SEVERITY_WARNING => "WARNING",
        PacketViolationWarningPacket::SEVERITY_FINAL_WARNING => "FINAL WARNING",
        PacketViolationWarningPacket::SEVERITY_TERMINATING_CONNECTION => "TERMINATION",
    ];

    public static string $resourcePath;

    /**
     * @param PluginBase $plugin
     */
    public function __construct(
        protected PluginBase $plugin
    ) {
        self::setInstance($this);
    }

    /**
     * @return PluginBase
     */
    public function getPlugin(): PluginBase
    {
        return $this->plugin;
    }

    /**
     * @return void
     * @throws HookAlreadyRegistered
     * @throws JsonException
     */
    public function enable(): void
    {
        $plugin = $this->getPlugin();
        if(!$plugin instanceof \Loader) {
            $plugin->onEnableStateChange(false);
            return;
        }

        self::$resourcePath = str_replace("\\", DIRECTORY_SEPARATOR, str_replace("/", DIRECTORY_SEPARATOR, Path::join($plugin->getFile(), "resources")));

        $net = ($server = $plugin->getServer())->getNetwork();

        $translators = [
            new v361PacketTranslator($server),
            new v486PacketTranslator($server),
            new LatestProtocol($server),
        ];

        $regInterface = function(string $ip, int $port, bool $ipv6) use ($server, $translators, $net){
            $rakNetAcceptor = new ColriaRakNetProtocolAcceptor([9, 10, 11]);
            $interface = new ColriaRakLibInterface($server, $rakNetAcceptor, $ip, $port, $ipv6);
            foreach($translators as $translator) $interface->registerTranslator($translator);
            $net->registerInterface($interface);
        };
        ($regInterface)($server->getIp(), $server->getPort(), false);
        if($server->getConfigGroup()->getConfigBool("enable-ipv6", true)){
            ($regInterface)($server->getIpV6(), $server->getPortV6(), true);
        }

        SimplePacketHandler::createMonitor($plugin)
            ->monitorIncoming(function(PacketViolationWarningPacket $pk, NetworkSession $src) use($plugin) : void{
                $severity = self::PACKET_VIOLATION_WARNING_SEVERITY[$pk->getSeverity()];
                $type = self::PACKET_VIOLATION_WARNING_TYPE[$pk->getType()] ?? "UNKNOWN [{$pk->getType()}]";
                $pkID = str_pad(dechex($pk->getPacketId()), 2, "0", STR_PAD_LEFT);
                $plugin->getLogger()->warning("Received $type Packet Violation ($severity) from {$src->getIp()} message: '{$pk->getMessage()}' Packet ID: 0x$pkID");
            });

        $plugin->getServer()->getPluginManager()->registerEvent(NetworkInterfaceRegisterEvent::class, function(NetworkInterfaceRegisterEvent $event) use($plugin) : void{
            $interface = $event->getInterface();
            if($interface instanceof ColriaRakLibInterface || (!$interface instanceof RakLibInterface && !$interface instanceof DedicatedQueryNetworkInterface)){
                return;
            }

            $cls = get_class($interface);
            $plugin->getLogger()->debug("Prevented network interface $cls from being registered");
            $event->cancel();
        }, EventPriority::NORMAL, $plugin);
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getCurrentPlayerProtocol(Player $player): int
    {
        $networkSession = $player->getNetworkSession();
        if(!$networkSession instanceof ColriaNetworkSession) {
            return ProtocolInfo::CURRENT_PROTOCOL;
        }

        return $networkSession->getPacketTranslator()::PROTOCOL_VERSION ?? ProtocolInfo::CURRENT_PROTOCOL;
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getCurrentPlayerVersion(Player $player): string
    {
        $protocol = $this->getCurrentPlayerProtocol($player);
        return match ($protocol) {
            v361PacketTranslator::PROTOCOL_VERSION => "1.12.1",
            v486PacketTranslator::PROTOCOL_VERSION => "1.18.30",
            LatestProtocol::PROTOCOL_VERSION => ProtocolInfo::MINECRAFT_VERSION,
        };
    }

    public function __destruct() {}
}