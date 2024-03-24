<?php

namespace PracticeCore\Zwuiix\network\proto;

use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\Server;
use PracticeCore\Zwuiix\network\ColriaNetworkSession;
use PracticeCore\Zwuiix\network\ColriaPacketBroadcaster;

abstract class PacketTranslator{

	public const PROTOCOL_VERSION = null;
	protected PacketSerializerFactory $pkSerializerFactory;
	protected ColriaPacketBroadcaster $broadcaster;

	public function __construct(Server $server){
		$this->broadcaster = new ColriaPacketBroadcaster($this, $server);
	}

	public function getBroadcaster() : ColriaPacketBroadcaster{
		return $this->broadcaster;
	}

	abstract public function setup(ColriaNetworkSession $session) : void;

	abstract public function handleIncoming(ServerboundPacket $pk) : ?ServerboundPacket;

	abstract public function handleOutgoing(ClientboundPacket $pk) : ?ClientboundPacket;

	abstract public function handleInGame(NetworkSession $session) : ?InGamePacketHandler;

	public function getPacketSerializerFactory() : PacketSerializerFactory{
		return $this->pkSerializerFactory;
	}

	abstract public function injectClientData(array &$data) : void;
}