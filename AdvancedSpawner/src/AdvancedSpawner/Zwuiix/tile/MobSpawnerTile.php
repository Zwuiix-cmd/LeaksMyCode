<?php

namespace AdvancedSpawner\Zwuiix\tile;

use AdvancedSpawner\Zwuiix\entity\MobEntity;
use AdvancedSpawner\Zwuiix\entity\MobStacker;
use AdvancedSpawner\Zwuiix\spawner\Spawner;
use AdvancedSpawner\Zwuiix\spawner\SpawnerHandler;
use AdvancedSpawner\Zwuiix\task\SpawnerTask;
use pocketmine\block\tile\Spawnable;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\particle\MobSpawnParticle;

class MobSpawnerTile extends Spawnable
{
    protected ?Spawner $spawner = null;
    private int $egg = 1;
    protected int $delay = 0;

    public function readSaveData(CompoundTag $nbt): void
    {
        $spawnerEggTag = $nbt->getTag("spawn_egg");

        if ($spawnerEggTag instanceof ShortTag)
        {
            $this->egg = $spawnerEggTag->getValue();
        }

        $tag = $nbt->getTag("spawnerType");
        if($tag instanceof StringTag) {
            $spawner = SpawnerHandler::getInstance()->getSpawnerByName($tag->getValue());
            if(!$spawner instanceof Spawner) {
                $this->close();
                return;
            }
            $this->setSpawnerType($spawner);
        }


    }

    protected function writeSaveData(CompoundTag $nbt): void
    {
        if(!$this->spawner) {
            return;
        }
        $nbt->setString("spawnerType", $this->spawner->getName());
        $nbt->setShort("spawn_egg", $this->egg);
    }

    /**
     * @param Spawner|null $spawner
     * @return void
     */
    public function setSpawnerType(?Spawner $spawner): void
    {
        $this->spawner = $spawner;
        $this->scheduledUpdate();
    }

    /**
     * @return Spawner
     */
    public function getSpawnerType(): Spawner
    {
        return $this->spawner;
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void {}

    /**
     * @return bool
     */
    public function onUpdate(): bool
    {
        if(!$this->getBlock()->asItem()->equals($this->spawner->getBlock(), false, false)) {
            $this->close();
            return false;
        }

        $this->timings->startTiming();
        if ($this->delay <= 0) {
            $pos = $this->getPosition()->add(mt_rand() / mt_getrandmax() * $this->spawner->getSpawnRadius(), mt_rand(0, 1), mt_rand() / mt_getrandmax() * $this->spawner->getSpawnRadius());
            $target = $this->getPosition()->getWorld()->getBlock($pos);

            for ($i = 0; $i < floor(mt_rand(floor($this->egg / 2), $this->egg)); $i++)
            {
                $entity = new MobEntity(Location::fromObject($target->getPosition(), $target->getPosition()->getWorld(), 0, 0), null, $this->spawner->getDrops(), $this->spawner->getName(), $this->spawner->getEntityId());
                $entity->spawnToAll();
                $target->getPosition()->getWorld()->addParticle($target->getPosition(), new MobSpawnParticle(floor($entity->getSize()->getWidth()), floor($entity->getSize()->getHeight())));

                $stack = new MobStacker($entity);
                $stack->stack();
            }

        } else $this->delay--;
        $this->timings->stopTiming();
        return true;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool
    {
        if(!$this->spawner instanceof Spawner) {
            return false;
        }

        if($this->closed) {
            return false;
        }

        $position = $this->getPosition();
        if (!$position->getWorld()->isChunkLoaded($position->getFloorX() >> 4, $position->getFloorZ() >> 4)) {
            return false;
        }

        return $position->getWorld()->getNearestEntity($position, $this->spawner->getSpawnDistance(), Human::class) instanceof Player;
    }

    public function scheduledUpdate(): void
    {
        new SpawnerTask($this->spawner->getSpawnSpeed(), $this);
    }

    /**
     * @return int
     */
    public function getEgg(): int
    {
        return $this->egg;
    }

    public function addEgg(): void
    {
        $this->egg++;
    }

}