<?php

namespace Zwuiix\AdvancedLightning\Utils;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class Utils
{
    use SingletonTrait;

    public function doLightning(Player $player): void
    {
        $location = $player->getLocation();

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

        Server::getInstance()->broadcastPackets(Server::getInstance()->getOnlinePlayers(), [$lightning, $thunder]);
    }
}