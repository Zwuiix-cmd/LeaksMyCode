<?php

namespace MusuiAntiCheat\Zwuiix\utils;

use Generator;
use pocketmine\block\UnknownBlock;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\World;
use function ceil;
use function floor;

final class WorldUtils {

    public const SEARCH_ALL = 0;
    public const SEARCH_TRANSPARENT = 1;
    public const SEARCH_SOLID = 2;

    /**
     * @param AxisAlignedBB $AABB
     * @param World $world
     * @param int $searchOption
     * @param float $epsilonXZ
     * @param float $epsilonY
     * @param bool $first
     * @return Generator
     */
    public static function checkBlocksInAABB(AxisAlignedBB $AABB, World $world, int $searchOption, float $epsilonXZ = 1, float $epsilonY = 1, bool $first = false): Generator {
        $minX = floor($AABB->minX - 1);
        $maxX = ceil($AABB->maxX + 1);
        $minY = floor($AABB->minY - 1);
        $maxY = ceil($AABB->maxY + 1);
        $minZ = floor($AABB->minZ - 1);
        $maxZ = ceil($AABB->maxZ + 1);
        $curr = $world->getBlockAt($minX, $minY, $minZ);
        switch ($searchOption) {
            case self::SEARCH_ALL:
                yield $curr;
                if ($first) {
                    return;
                }
                for ($x = $minX; $x <= $maxX; $x += $epsilonXZ) {
                    for ($y = $minY; $y <= $maxY; $y += $epsilonY) {
                        for ($z = $minZ; $z <= $maxZ; $z += $epsilonXZ) {
                            yield $world->getBlockAt($x, $y, $z);
                        }
                    }
                }
                return;
            case self::SEARCH_TRANSPARENT:
                if ($curr->hasEntityCollision()) {
                    yield $curr;
                    if ($first) {
                        return;
                    }
                }
                for ($x = $minX; $x <= $maxX; $x += $epsilonXZ) {
                    for ($y = $minY; $y <= $maxY; $y += $epsilonY) {
                        for ($z = $minZ; $z <= $maxZ; $z += $epsilonXZ) {
                            $block = $world->getBlockAt($x, $y, $z);
                            if ($block->hasEntityCollision()) {
                                yield $block;
                                if ($first) {
                                    return;
                                }
                            }
                        }
                    }
                }
                return;
            case self::SEARCH_SOLID:
                if ($curr->isSolid() || $curr instanceof UnknownBlock) {
                    yield $curr;
                    if ($first) {
                        return;
                    }
                }
                for ($x = $minX; $x <= $maxX; $x += $epsilonXZ) {
                    for ($y = $minY; $y <= $maxY; $y += $epsilonY) {
                        for ($z = $minZ; $z <= $maxZ; $z += $epsilonXZ) {
                            $block = $world->getBlockAt($x, $y, $z);
                            if ($block->isSolid() || $block instanceof UnknownBlock) {
                                yield $block;
                                if ($first) {
                                    return;
                                }
                            }
                        }
                    }
                }
                return; // don't you dare mention this line
        }
    }

    /**
     * @param AxisAlignedBB $AABB
     * @param World $world
     * @return AxisAlignedBB[]
     */
    public static function getCollisionBBList(AxisAlignedBB $AABB, World $world): array {
        $list = [];
        $AABB = $AABB->expandedCopy(0.0001, 0, 0.0001);
        $minX = floor($AABB->minX - 1);
        $maxX = ceil($AABB->maxX + 1);
        $minY = floor($AABB->minY - 1);
        $maxY = ceil($AABB->maxY + 1);
        $minZ = floor($AABB->minZ - 1);
        $maxZ = ceil($AABB->maxZ + 1);
        for ($z = $minZ; $z <= $maxZ; ++$z) {
            for ($x = $minX; $x <= $maxX; ++$x) {
                for ($y = $minY; $y <= $maxY; ++$y) {
                    $block = $world->getBlockAt($x, $y, $z);
                    if (/*!$block->canPassThrough() ||*/ $block instanceof UnknownBlock) {
                        foreach ($block->getCollisionBoxes() as $bb) {
                            if ($bb->intersectsWith($AABB)) {
                                $list[] = $bb;
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

}