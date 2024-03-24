<?php

namespace PracticeCore\Zwuiix\network\proto\chunk\serializer;

use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\SubChunk;
use PracticeCore\Zwuiix\network\proto\PacketSerializerFactory;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

interface MVChunkSerializer{
	public function getPaddingSize(Chunk $chunk) : int;

	public function getSubChunkCount(Chunk $chunk) : int;

	public function serializeFullChunk(Chunk $chunk, PacketSerializerFactory $factory, ?string $tiles = null) : string;

	public function serializeSubChunk(SubChunk $subChunk, IRuntimeBlockMapping $blockMapper, PacketSerializer $stream, bool $persistentBlockStates) : void;

	public function serializeTiles(Chunk $chunk) : string;
}