<?php

namespace PlutooCore\handlers;

use Closure;
use JsonException;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class ShopHandler
{
    public const TYPE_BUY = 0;
    public const TYPE_SELL = 1;

    use SingletonTrait;

    protected Config $config;

    protected Closure $closure;
    public array $format = [
        "§r{TYPE}",
        "§r§5{ITEM}",
        "§r§5{COUNT}",
        "§r§9{PRICE}$",
    ];

    public function __construct()
    {
        self::setInstance($this);
        $this->config = new Config(Path::join(\MusuiEssentials::getInstance()->getDataFolder(), "shop.json"), Config::JSON);
        $this->closure = function (int $type, string $item, int $count, int $price) {
            return str_replace([
                "{TYPE}",
                "{ITEM}",
                "{COUNT}",
                "{PRICE}",
            ], [
                $type == self::TYPE_BUY ? "§9Achat" : "§9Vente",
                $item,
                $count,
                $price
            ], $this->format);
        };
    }

    /**
     * @param Vector3 $vector3
     * @param array $data
     * @return void
     * @throws JsonException
     */
    public function add(Vector3 $vector3, array $data): void
    {
        $this->config->set("{$vector3->getFloorX()}:{$vector3->getFloorY()}:{$vector3->getFloorZ()}", $data);
        $this->config->save();
    }

    /**
     * @param Vector3 $vector3
     * @return array
     */
    public function get(Vector3 $vector3): array
    {
        return $this->config->get("{$vector3->getFloorX()}:{$vector3->getFloorY()}:{$vector3->getFloorZ()}", []);
    }

    /**
     * @param Vector3 $vector3
     * @return void
     * @throws JsonException
     */
    public function remove(Vector3 $vector3): void
    {
        $this->config->remove("{$vector3->getFloorX()}:{$vector3->getFloorY()}:{$vector3->getFloorZ()}");
        $this->config->save();
    }

    /**
     * @return Closure
     */
    public function format(): Closure
    {
        return $this->closure;
    }
}