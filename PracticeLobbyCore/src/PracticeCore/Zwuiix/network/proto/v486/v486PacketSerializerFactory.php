<?php

namespace PracticeCore\Zwuiix\network\proto\v486;

use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use PracticeCore\Zwuiix\network\proto\chunk\serializer\ExtendedYChunkSerializer;
use PracticeCore\Zwuiix\network\proto\IMVPacketSerializerContext;
use PracticeCore\Zwuiix\network\proto\MVPacketSerializer;
use PracticeCore\Zwuiix\network\proto\MVPacketSerializerContext;
use PracticeCore\Zwuiix\network\proto\PacketSerializerFactory;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

class v486PacketSerializerFactory implements PacketSerializerFactory{

	private ExtendedYChunkSerializer $chunkSerializer;

	public function __construct(
		private ItemTypeDictionary $itemDictionary,
		private IRuntimeBlockMapping $blockMapping,
	){
		$this->chunkSerializer = new ExtendedYChunkSerializer();
	}

	public function newEncoder(IMVPacketSerializerContext $context) : MVPacketSerializer{
		return v486PacketSerializer::newEncoder($context);
	}

	public function newDecoder(string $buffer, int $offset, IMVPacketSerializerContext $context) : MVPacketSerializer{
		return v486PacketSerializer::newDecoder($buffer, $offset, $context);
	}

	public function getClass() : string{
		return v486PacketSerializer::class;
	}

	public function newSerializerContext() : IMVPacketSerializerContext{
		return new MVPacketSerializerContext($this->itemDictionary, $this->blockMapping);
	}

	public function getBlockMapping() : IRuntimeBlockMapping{
		return $this->blockMapping;
	}

	public function getChunkSerializer() : ExtendedYChunkSerializer{
		return $this->chunkSerializer;
	}
}