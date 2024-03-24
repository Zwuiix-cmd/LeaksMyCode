<?php

namespace Zwuiix\AdvancedNexus\Trait;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\world\World;
use Zwuiix\AdvancedNexus\Commands\NexusCommand;
use Zwuiix\AdvancedNexus\Entities\NexusEntity;
use Zwuiix\AdvancedNexus\Lib\xenialdan\apibossbar\API;
use Zwuiix\AdvancedNexus\Task\NexusTask;

trait LoaderTrait
{
    use DataTrait;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->saveDefaultConfig();
        $this->initData();

        API::load($this);
        $this->getServer()->getCommandMap()->register("AdvancedNexus", new NexusCommand($this, "nexus", "Permet de démarrer ou de stopper un évènement nexus!"));

        EntityFactory::getInstance()->register(NexusEntity::class, function (World $world, CompoundTag $nbt): NexusEntity {
            return new NexusEntity(EntityDataHelper::parseLocation($nbt, $world), $this->getData(), $nbt);
        }, ['NexusEntity']);

        foreach (Server::getInstance()->getWorldManager()->getWorldByName($this->getData()->getNested("entity.position.world"))->getEntities() as $entity){
            if($entity instanceof NexusEntity) $this->getScheduler()->scheduleRepeatingTask(new NexusTask($this, $entity, $this->getData()), 20);
        }
    }
}