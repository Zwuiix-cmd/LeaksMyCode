<?php

namespace PlutooCore\task;

use MusuiEssentials\MusuiPlayer;
use PlutooCore\handlers\OptionsHandler;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\scheduler\Task;

class ImmobileTask extends Task
{
    public function __construct(
        protected MusuiPlayer $player,
    )
    {
        $this->player->setNoClientPredictions(true);
        $pos = $this->player->getPosition();
        foreach ($pos->getWorld()->getViewersForPosition($pos) as $value) {
            if($value instanceof MusuiPlayer && $value->isConnected()) {
                if(!OptionsHandler::getInstance()->get($value->getName(), "lightning", true)) continue;
                $value->getNetworkSession()->sendDataPacket(AddActorPacket::create(
                    ($id = Entity::nextRuntimeId()), $id,
                    "minecraft:lightning_bolt", $pos->asVector3(), null, 0.0, 0.0, 0.0, 0.0,
                    [], [], new PropertySyncData([], []), []));
                $value->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1));
            }
        }
        \PlutooCore::getInstance()->getScheduler()->scheduleDelayedTask($this, 20 * 3);
    }

    public function onRun(): void
    {
        if (!$this->player->isConnected()) {
            $this->getHandler()?->cancel();
            return;
        }

        $this->player->setNoClientPredictions(false);
    }
}