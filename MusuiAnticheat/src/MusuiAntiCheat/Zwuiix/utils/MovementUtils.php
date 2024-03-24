<?php

namespace MusuiAntiCheat\Zwuiix\utils;

use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\math\Vector3;
use function cos;
use function max;
use function sin;
use function sqrt;

final class MovementUtils {

    public static function moveFlying(float $forward, float $strafe, float $friction, float $yaw): Vector3
    {
        $var1 = ($forward ** 2) + ($strafe ** 2);
        if ($var1 >= 1E-4) {
            $var1 = max(sqrt($var1), 1);
            $var1 = $friction / $var1;
            $strafe *= $var1;
            $forward *= $var1;
            $var2 = sin($yaw * M_PI / 180);
            $var3 = cos($yaw * M_PI / 180);
            return new Vector3($strafe * $var3 - $forward * $var2, 0, $forward * $var3 + $strafe * $var2);
        }
        return new Vector3(0, 0, 0);
    }

    /**
     * @return Vector3 - An estimated position of where the player should be next.
     */
    public static function doCollisions(Session $session): Vector3 {
        $dx = $session->currentMoveDelta->x;
        $dy = $session->currentMoveDelta->y;
        $dz = $session->currentMoveDelta->z;
        $movX = $dx;
        $movY = $dy;
        $movZ = $dz;

        $session->ySize *= 0.4;
        $oldBB = AABB::fromPosition($session->lastLocation, 0.7001, 2.0001)->toAABB();
        $oldBBClone = clone $oldBB;

        $world = $session->getPlayer()->getWorld();

        $list = WorldUtils::getCollisionBBList($oldBB->addCoord($dx, $dy, $dz), $world);

        foreach ($list as $bb) {
            $dy = $bb->calculateYOffset($oldBB, $dy);
        }

        $oldBB->offset(0, $dy, 0);

        $fallingFlag = $session->getPlayer()->onGround || ($dy != $movY && $movY < 0);

        foreach ($list as $bb) {
            $dx = $bb->calculateXOffset($oldBB, $dx);
        }

        $oldBB->offset($dx, 0, 0);

        foreach ($list as $bb) {
            $dz = $bb->calculateZOffset($oldBB, $dz);
        }

        $oldBB->offset(0, 0, $dz);

        if ($fallingFlag && ($movX != $dx || $movZ != $dz)) {
            $cx = $dx;
            $cy = $dy;
            $cz = $dz;
            $dx = $movX;
            $dy = MovementConstants::STEP_HEIGHT;
            $dz = $movZ;

            $oldBBClone2 = clone $oldBB;
            $oldBB = $oldBBClone;

            $list = WorldUtils::getCollisionBBList($oldBB->addCoord($dx, $dy, $dz), $world);

            foreach ($list as $bb) {
                $dy = $bb->calculateYOffset($oldBB, $dy);
            }

            $oldBB->offset(0, $dy, 0);

            foreach ($list as $bb) {
                $dx = $bb->calculateXOffset($oldBB, $dx);
            }

            $oldBB->offset($dx, 0, 0);

            foreach ($list as $bb) {
                $dz = $bb->calculateZOffset($oldBB, $dz);
            }

            $oldBB->offset(0, 0, $dz);

            $reverseDY = -$dy;
            foreach ($list as $bb) {
                $reverseDY = $bb->calculateYOffset($oldBB, $reverseDY);
            }
            $dy += $reverseDY;
            $oldBB->offset(0, $reverseDY, 0);

            if (($cx ** 2 + $cz ** 2) >= ($dx ** 2 + $dz ** 2)) {
                /* $dx = $cx;
                $dy = $cy;
                $dz = $cz; */
                $oldBB = $oldBBClone2;
            } else {
                $session->ySize += $dy;
            }
        }

        $position = new Vector3(0, 0, 0);
        $position->x = ($oldBB->minX + $oldBB->maxX) / 2;
        $position->y = $oldBB->minY - $session->ySize;
        $position->z = ($oldBB->minZ + $oldBB->maxZ) / 2;
        return $position;
    }

}