<?php

namespace MusuiAntiCheat\Zwuiix\utils;

use pocketmine\block\Block;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;

class AABB
{
    public function __construct(
        public float $minX,
        public float $minY,
        public float $minZ,
        public float $maxX,
        public float $maxY,
        public float $maxZ
    ) {}

    public static function fromPosition(Vector3 $pos, float $width = 0.3, float $height = 1.8): AABB
    {
        return new AABB($pos->x - $width, $pos->y, $pos->z - $width, $pos->x + $width, $pos->y + $height, $pos->z + $width);
    }

    public static function fromBlock(Block $block): AABB {
        return new AABB(($pos = $block->getPosition())->getX(), $pos->getY(), $pos->getZ(), $pos->getX() + 1, $pos->getY() + 1, $pos->getZ() + 1);
    }

    public static function getEyePosition(Vector3 $vector3, float $eyeHeight): Vector3
    {
        return new Vector3($vector3->x, $vector3->y + $eyeHeight, $vector3->z);
    }

    public function distanceFromVector(Vector3 $vector): float {
        $distX = max($this->minX - $vector->x, 0, $vector->x - $this->maxX);
        $distY = max($this->minY - $vector->y, 0, $vector->y - $this->maxY);
        $distZ = max($this->minZ - $vector->z, 0, $vector->z - $this->maxZ);
        return sqrt(($distX ** 2) + ($distY ** 2) + ($distZ ** 2));
    }

    public function calculateIntercept(Vector3 $pos1, Vector3 $pos2) : ?RayTraceResult{
        return $this->toAABB()->isVectorInside($pos1) ? new RayTraceResult($this->toAABB(), 0, clone new Vector3(0, 0, 0)) : $this->toAABB()->calculateIntercept($pos1, $pos2);
    }

    public function grow(float $x, float $y, float $z) : AABB{
        return new AABB($this->minX - $x, $this->minY - $y, $this->minZ - $z, $this->maxX + $x, $this->maxY, $this->maxZ);
    }

    public function toAABB() : AxisAlignedBB{
        return new AxisAlignedBB($this->minX, $this->minY, $this->minZ, $this->maxX, $this->maxY, $this->maxZ);
    }
}