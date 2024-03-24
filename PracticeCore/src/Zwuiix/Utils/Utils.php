<?php

namespace Zwuiix\Utils;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use Zwuiix\Items\BowItem;
use Zwuiix\Items\CustomItem;
use Zwuiix\Items\EnchantedBook;
use Zwuiix\Player\User;

class Utils
{
    use SingletonTrait;

    /**
     * @param bool $resp
     * @return string
     */
    public static function boolToString(bool $resp): string
    {
        return $resp ? "§2Activer" : "§cDésactiver";
    }

    /**
     * @param Player $player
     * @return int|string|null
     */
    public function pingColor(Player $player): int|string|null
    {
        $ping=$player->getNetworkSession()->getPing();
        if($ping < 100){
            $ping = "§2{$ping}";
        }elseif($ping < 300){
            $ping = "§6{$ping}";
        } else {
            $ping = "§c{$ping}";
        }
        return $ping;
    }

    public static function getPotionsCount(User $player, int $meta = 22): int
    {
        return count($player->getInventory()->all(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, $meta)));
    }

    public function doLightning(Location $location, User $user): void
    {
        $lightning = new AddActorPacket();
        $lightning->actorUniqueId = Entity::nextRuntimeId();
        $lightning->actorRuntimeId = $lightning->actorUniqueId;
        $lightning->type = 'minecraft:lightning_bolt';
        $lightning->position = $location->asVector3();
        $lightning->motion = null;
        $lightning->pitch = $location->getPitch();
        $lightning->yaw = $location->getYaw();
        $lightning->headYaw = 0.0;
        $lightning->attributes = [];
        $lightning->metadata = [];
        $lightning->syncedProperties=new PropertySyncData([], []);
        $lightning->links = [];

        $thunder = new PlaySoundPacket();
        $thunder->soundName = 'ambient.weather.thunder';
        $thunder->x = $location->getX();
        $thunder->y = $location->getY();
        $thunder->z = $location->getZ();
        $thunder->volume = 0.5;
        $thunder->pitch = 1;

        $user->getNetworkSession()->sendDataPacket($lightning);
        $user->getNetworkSession()->sendDataPacket($thunder);
    }
}