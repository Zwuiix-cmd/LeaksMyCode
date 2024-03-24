<?php

namespace AdvancedHealthTag\Zwuiix\handler;

use AdvancedHealthTag\Zwuiix\Main;
use AdvancedHealthTag\Zwuiix\utils\FormatValueColor;
use JsonException;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\Utils;

class HealthTagHandler
{
    use SingletonTrait;

    protected array $data = array();

    public function load(Config $config): void
    {
        $this->data = $config->getAll();
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $name, mixed $default = "undefined"): mixed
    {
        return $this->data[$name] ?? $default;
    }

    public function updatePlayer(Player $player, bool $hasAttacked = false): void
    {
        $v = HealthTagHandler::getInstance();
        $data = "";
        switch ($v->get("type", "health")) {
            case "percentage":
                $data .= FormatValueColor::getInstance()->format(round($player->getHealth() * 100 / $player->getMaxHealth())) . "%";
                break;
            case "health":
                $data .= FormatValueColor::getInstance()->format(round($player->getHealth()));
                break;
        }

        switch ($v->get("visual", "scoretag")) {
            case "scoretag":
                $player->setScoreTag($data);
                break;
            case "actionbar":
                if(!$hasAttacked) break;
                $last = $player->getLastDamageCause();
                if($last instanceof EntityDamageByEntityEvent) {
                    $damager = $last->getDamager();
                    if($damager instanceof Player) {
                        $damager->sendActionBarMessage($data . "%%");
                    }
                }
                break;
        }
    }
}