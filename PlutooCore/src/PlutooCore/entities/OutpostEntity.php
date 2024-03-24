<?php

namespace PlutooCore\entities;

use MusuiEssentials\utils\DateFormatter;
use PlutooCore\task\OutpostTask;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class OutpostEntity extends Entity
{
    const PERIOD = 20;
    protected bool $gravityEnabled = false;
    protected float $gravity = 0.0;
    private int $tickToUpdate;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        $this->tickToUpdate = self::PERIOD;
        $this->setNameTagAlwaysVisible();
        $this->setNameTag($this->getUpdate());
        $this->setScale(0.001);
        $this->setNoClientPredictions();
    }

    private function getUpdate(): string
    {
        return !is_null($fac = OutpostTask::getInstance()->actualFaction) ?
            "§9Outpost\n§5Outpost controlé par la faction §9{$fac}\n§5Disponible dans §9" . DateFormatter::toString(time() + OutpostTask::getInstance()->currentOutpost) . "\n§5Récompenses dans §9" . DateFormatter::toString(time() + OutpostTask::getInstance()->nextReward) :
            "§9Outpost\n§cAucune faction ne controle\n§5Controle de l'§9Outpost§5 dans §9" . DateFormatter::toString(time() + OutpostTask::getInstance()->currentOutpost);
    }

    public static function getNetworkTypeId(): string{return EntityIds::PLAYER;}
    public function getName(): string {return "OutpostEntity";}
    public function attack(EntityDamageEvent $source): void{}
    protected function getInitialDragMultiplier(): float {return 0;}
    protected function getInitialGravity(): float {return 0;}
    protected function getInitialSizeInfo(): EntitySizeInfo {return new EntitySizeInfo(0.7, 0.4);}

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        if ($this->isClosed()) return false;
        if ($this->isAlive()) {
            --$this->tickToUpdate;

            if ($this->tickToUpdate <= 0) {
                $this->setNameTag($this->getUpdate());
                $this->tickToUpdate = self::PERIOD;
            }
        }
        return parent::entityBaseTick($tickDiff);
    }
}
