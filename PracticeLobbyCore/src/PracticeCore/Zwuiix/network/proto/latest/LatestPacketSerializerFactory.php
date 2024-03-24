<?php

namespace PracticeCore\Zwuiix\network\proto\latest;

use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use PracticeCore\Zwuiix\network\proto\chunk\serializer\ExtendedYChunkSerializer;
use PracticeCore\Zwuiix\network\proto\chunk\serializer\MVChunkSerializer;
use PracticeCore\Zwuiix\network\proto\IMVPacketSerializerContext;
use PracticeCore\Zwuiix\network\proto\MVPacketSerializer;
use PracticeCore\Zwuiix\network\proto\MVPacketSerializerContext;
use PracticeCore\Zwuiix\network\proto\PacketSerializerFactory;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

class LatestPacketSerializerFactory implements PacketSerializerFactory{

	private ExtendedYChunkSerializer $chunkSerializer;

	public function __construct(
		private IRuntimeBlockMapping $blockMapping
	){
		$this->chunkSerializer = new ExtendedYChunkSerializer();
	}

	public function newEncoder(IMVPacketSerializerContext $context) : MVPacketSerializer{
		return LatestPacketSerializer::newEncoder($context);
	}

	public function newDecoder(string $buffer, int $offset, IMVPacketSerializerContext $context) : MVPacketSerializer{
		return LatestPacketSerializer::newDecoder($buffer, $offset, $context);
	}

	public function getClass() : string{
		return LatestPacketSerializer::class;
	}

	public function newSerializerContext() : IMVPacketSerializerContext{
		return new MVPacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary(), $this->blockMapping);
	}

	public function getBlockMapping() : IRuntimeBlockMapping{
		return $this->blockMapping;
	}

	public function getChunkSerializer() : MVChunkSerializer{
		return $this->chunkSerializer;
	}
}