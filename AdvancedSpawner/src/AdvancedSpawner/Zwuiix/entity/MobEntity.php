<?php

namespace AdvancedSpawner\Zwuiix\entity;

use pocketmine\entity\Attribute;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;

class MobEntity extends Living
{
    public CompoundTag $nbt;

    public function __construct(Location $location, ?CompoundTag $nbt = null, protected array $drops = [], protected string $name = "", protected string $entityId = "")
    {
        if($nbt === null) $nbt = new CompoundTag();
        $this->nbt=$nbt;

        parent::__construct($location, $nbt);
        if($this->name === "") {
            $this->flagForDespawn();
        }
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.62, 1);
    }

    public static function getNetworkTypeId(): string
    {
        return "";
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function sendSpawnPacket(Player $player) : void{
        $player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
            $this->getId(), //TODO: actor unique ID
            $this->getId(),
            $this->entityId,
            $this->location->asVector3(),
            $this->getMotion(),
            $this->location->pitch,
            $this->location->yaw,
            $this->location->yaw,
            $this->location->yaw,
            array_map(function(Attribute $attr) : NetworkAttribute{
                return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue(), []);
            }, $this->attributeMap->getAll()),
            $this->getAllNetworkData(),
            new PropertySyncData([], []),
            [] //TODO: entity links
        ));
    }

    /**
     * @return array|Item[]
     */
    public function getDrops(): array
    {
        return $this->drops;
    }
}