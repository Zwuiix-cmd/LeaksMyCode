<?php

namespace Zwuiix\Utils;

use JsonException;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;
use Zwuiix\Player\User;
use function chr;
use function ord;
use function strlen;

class Skin
{
    use SingletonTrait;

    public const SKIN_WIDTH_MAP = [
        64 * 32 * 4   => 64,
        64 * 64 * 4   => 64,
        128 * 128 * 4 => 128,
    ];
    public const SKIN_HEIGHT_MAP = [
        64 * 32 * 4   => 32,
        64 * 64 * 4   => 64,
        128 * 128 * 4 => 128,
    ];

    private int $percentage = 90;

    public function checkSkin(User $player, ?string $skinData = null) : bool {
        $skinData ??= $player->getSkin()->getSkinData();
        $size = strlen($skinData);
        $width = self::SKIN_WIDTH_MAP[$size];
        $height = self::SKIN_HEIGHT_MAP[$size];
        $pos = -1;
        $pixelsNeeded = (int)((100 - $this->percentage) / 100 * ($width * $height)); // visible pixels needed
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (ord($skinData[$pos += 4]) === 255) {
                    if (--$pixelsNeeded === 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return \pocketmine\entity\Skin
     * @throws JsonException
     */
    public function randomSkin() : \pocketmine\entity\Skin
    {
        $bytes = '';
        for ($i = 0; $i < 2048; $i++) {
            $bytes .= chr(mt_rand(0, 255)) . chr(mt_rand(0, 255)) . chr(mt_rand(0, 255)) . chr(255);
        }
        return new \pocketmine\entity\Skin(Uuid::uuid4()->toString(), $bytes, "", "geometry.humanoid.custom", "");
    }
}