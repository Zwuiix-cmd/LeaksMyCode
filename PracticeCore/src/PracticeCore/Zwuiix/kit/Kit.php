<?php

namespace PracticeCore\Zwuiix\kit;

use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use PracticeCore\Zwuiix\session\Session;

class Kit
{
    /**
     * @param string $name
     * @param Item[] $inventoryContents
     * @param Item[] $armorContents
     * @param Effect[] $effects
     */
    public function __construct(
        protected string $name,
        protected array $inventoryContents,
        protected array $armorContents,
        protected array $effects,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getInventoryContents(): array
    {
        return $this->inventoryContents;
    }

    /**
     * @return array
     */
    public function getArmorContents(): array
    {
        return $this->armorContents;
    }

    /**
     * @return Effect[]
     */
    public function getEffects(): array
    {
        return $this->effects;
    }

    public function give(Session $session): void
    {
        if(count($this->getEffects()) !== 0) {
            $session->getEffects()->clear();
            foreach ($this->getEffects() as $effect) $session->getEffects()->add(new EffectInstance($effect, 9999999, 0, false));
        }

        $session->setHealth(20);
        $session->getCursorInventory()->clearAll();
        $session->getInventory()->setContents($this->getInventoryContents());
        $session->getArmorInventory()->setContents($this->getArmorContents());
    }
}