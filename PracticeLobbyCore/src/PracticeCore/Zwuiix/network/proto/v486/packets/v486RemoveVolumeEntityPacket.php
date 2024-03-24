<?php

namespace PracticeCore\Zwuiix\network\proto\v486\packets;

use pocketmine\network\mcpe\protocol\RemoveVolumeEntityPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use PracticeCore\Zwuiix\libs\CortexPE\std\ReflectionUtils;
use ReflectionException;

class v486RemoveVolumeEntityPacket extends RemoveVolumeEntityPacket{

	/**
	 * @throws ReflectionException
	 */
	public static function fromLatest(RemoveVolumeEntityPacket $packet) : self{
		$npk = new self();
		ReflectionUtils::setProperty(RemoveVolumeEntityPacket::class, $npk, "entityNetId", $packet->getEntityNetId());
		return $npk;
	}

	/**
	 * @throws ReflectionException
	 */
	protected function decodePayload(PacketSerializer $in) : void{
		ReflectionUtils::setProperty(RemoveVolumeEntityPacket::class, $this, "entityNetId", $in->getUnsignedVarInt());
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->getEntityNetId());
	}
}