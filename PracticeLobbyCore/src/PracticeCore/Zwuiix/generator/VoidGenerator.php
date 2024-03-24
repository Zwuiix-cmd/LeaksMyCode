<?php

namespace PracticeCore\Zwuiix\generator;

use pocketmine\block\BlockLegacyIds;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator {

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        /** @phpstan-var Chunk $chunk */
        $chunk = $world->getChunk($chunkX, $chunkZ);

        if($chunkX == 16 && $chunkZ == 16) {
            $chunk->setFullBlock(0, 64, 0, BlockLegacyIds::GRASS);
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
}