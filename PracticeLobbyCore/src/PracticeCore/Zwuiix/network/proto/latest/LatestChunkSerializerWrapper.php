<?php

namespace PracticeCore\Zwuiix\network\proto\latest;

use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializerContext;
use pocketmine\network\mcpe\serializer\ChunkSerializer;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\SubChunk;
use PracticeCore\Zwuiix\network\proto\chunk\serializer\MVChunkSerializer;
use PracticeCore\Zwuiix\network\proto\PacketSerializerFactory;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

class LatestChunkSerializerWrapper implements MVChunkSerializer{

	public function getSubChunkCount(Chunk $chunk) : int{
		return ChunkSerializer::getSubChunkCount($chunk) + ChunkSerializer::LOWER_PADDING_SIZE;
	}

	public function serializeFullChunk(Chunk $chunk, PacketSerializerFactory $factory, ?string $tiles = null) : string{
		return ChunkSerializer::serializeFullChunk($chunk, RuntimeBlockMapping::getInstance(), new PacketSerializerContext(GlobalItemTypeDictionary::getInstance()->getDictionary()), $tiles);
	}

	public function serializeSubChunk(SubChunk $subChunk, IRuntimeBlockMapping $blockMapper, PacketSerializer $stream, bool $persistentBlockStates) : void{
		ChunkSerializer::serializeSubChunk($subChunk, RuntimeBlockMapping::getInstance(), $stream, $persistentBlockStates);
	}

	public function serializeTiles(Chunk $chunk) : string{
		return ChunkSerializer::serializeTiles($chunk);
	}

	public function getPaddingSize(Chunk $chunk) : int{
		return 0; // no padding, we don't support below-bedrock on older versions
	}
}