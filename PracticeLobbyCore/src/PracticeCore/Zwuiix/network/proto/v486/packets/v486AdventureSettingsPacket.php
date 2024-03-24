<?php

namespace PracticeCore\Zwuiix\network\proto\v486\packets;

use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;

class v486AdventureSettingsPacket extends DataPacket implements ClientboundPacket, ServerboundPacket{
	public const NETWORK_ID = 0x37;

	/**
	 * This constant is used to identify flags that should be set on the second field. In a sensible world, these
	 * flags would all be set on the same packet field, but as of MCPE 1.2, the new abilities flags have for some
	 * reason been assigned a separate field.
	 */
	public const BITFLAG_SECOND_SET = 1 << 16;

	public const WORLD_IMMUTABLE = 0x01;
	public const NO_PVP = 0x02;

	public const AUTO_JUMP = 0x20;
	public const ALLOW_FLIGHT = 0x40;
	public const NO_CLIP = 0x80;
	public const WORLD_BUILDER = 0x100;
	public const FLYING = 0x200;
	public const MUTED = 0x400;

	public const MINE = 0x01 | self::BITFLAG_SECOND_SET;
	public const DOORS_AND_SWITCHES = 0x02 | self::BITFLAG_SECOND_SET;
	public const OPEN_CONTAINERS = 0x04 | self::BITFLAG_SECOND_SET;
	public const ATTACK_PLAYERS = 0x08 | self::BITFLAG_SECOND_SET;
	public const ATTACK_MOBS = 0x10 | self::BITFLAG_SECOND_SET;
	public const OPERATOR = 0x20 | self::BITFLAG_SECOND_SET;
	public const TELEPORT = 0x80 | self::BITFLAG_SECOND_SET;
	public const BUILD = 0x100 | self::BITFLAG_SECOND_SET;
	public const DEFAULT = 0x200 | self::BITFLAG_SECOND_SET;

	public int $flags = 0;
	public int $commandPermission = CommandPermissions::NORMAL;
	public int $flags2 = -1;
	public int $playerPermission = PlayerPermissions::MEMBER;
	public int $customFlags = 0; //...
	public int $targetActorUniqueId; //This is a little-endian long, NOT a var-long. (WTF Mojang)

	/**
	 * @generate-create-func
	 */
	public static function create(int $flags, int $commandPermission, int $flags2, int $playerPermission, int $customFlags, int $targetActorUniqueId) : self{
		$result = new self;
		$result->flags = $flags;
		$result->commandPermission = $commandPermission;
		$result->flags2 = $flags2;
		$result->playerPermission = $playerPermission;
		$result->customFlags = $customFlags;
		$result->targetActorUniqueId = $targetActorUniqueId;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->flags = $in->getUnsignedVarInt();
		$this->commandPermission = $in->getUnsignedVarInt();
		$this->flags2 = $in->getUnsignedVarInt();
		$this->playerPermission = $in->getUnsignedVarInt();
		$this->customFlags = $in->getUnsignedVarInt();
		$this->targetActorUniqueId = $in->getLLong();
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putUnsignedVarInt($this->flags);
		$out->putUnsignedVarInt($this->commandPermission);
		$out->putUnsignedVarInt($this->flags2);
		$out->putUnsignedVarInt($this->playerPermission);
		$out->putUnsignedVarInt($this->customFlags);
		$out->putLLong($this->targetActorUniqueId);
	}

	public function getFlag(int $flag) : bool{
		if(($flag & self::BITFLAG_SECOND_SET) !== 0){
			return ($this->flags2 & $flag) !== 0;
		}

		return ($this->flags & $flag) !== 0;
	}

	public function setFlag(int $flag, bool $value) : void{
		if(($flag & self::BITFLAG_SECOND_SET) !== 0){
			$flagSet = &$this->flags2;
		}else{
			$flagSet = &$this->flags;
		}

		if($value){
			$flagSet |= $flag;
		}else{
			$flagSet &= ~$flag;
		}
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return true; // this isn't handled anymore, it is useless
	}
}
