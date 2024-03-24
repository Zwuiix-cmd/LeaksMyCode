<?php

namespace PlutooCore\item;

use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Snowball;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;

class InfiniteSnowball extends Snowball
{
    public function getMaxStackSize() : int
    {
        return 64;
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @param array $returnedItems
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
        $location = $player->getLocation();

        $projectile = $this->createEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
        $projectile->setMotion($directionVector->multiply($this->getThrowForce()));

        $projectileEv = new ProjectileLaunchEvent($projectile);
        $projectileEv->call();
        if($projectileEv->isCancelled()){
            $projectile->flagForDespawn();
            return ItemUseResult::FAIL;
        }

        $projectile->spawnToAll();
        $location->getWorld()->addSound($location, new ThrowSound());

        $durability = $this->getNamedTag()->getInt("durability", 200);
        $this->getNamedTag()->setInt("durability", ($newDura = $durability - 1));
        $this->setLore(["§r§7Durabilité: {$newDura}/200"]);
        if($newDura <= 0) $this->pop();

        $player->sendActionBarMessage("§c{$newDura}/200");

        return ItemUseResult::SUCCESS;
    }
}