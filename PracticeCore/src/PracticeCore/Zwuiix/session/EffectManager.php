<?php

namespace PracticeCore\Zwuiix\session;

use pocketmine\color\Color;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\EffectManager as EffectManagerPM;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\MobEffectPacket;

class EffectManager extends EffectManagerPM
{
    public function __construct(
        protected Session $session
    ){
        parent::__construct($session);
    }

    /*** @var EffectInstance[] */
    protected array $infiniteEffects = array();

    /**
     * @return EffectInstance[]
     */
    public function &getInfiniteEffects(): array
    {
        return $this->infiniteEffects;
    }

    /**
     * @param Effect $effect
     * @param int $amplifier
     * @param bool $visible
     * @param bool $ambient
     * @param Color|null $overrideColor
     * @return void
     */
    public function addInfinite(Effect $effect, int $amplifier = 0, bool $visible = true, bool $ambient = false, ?Color $overrideColor = null): void
    {
        $effectInstance = new EffectInstance($effect, 999999, $amplifier, $visible, $ambient, $overrideColor);
        $this->add($effectInstance);
        $this->session->getNetworkSession()->sendDataPacket(MobEffectPacket::remove($this->session->getId(), EffectIdMap::getInstance()->toId($effect)));
        $name = $effectInstance->getType()->getName();
        if($name instanceof Translatable) $name = $name->getText();
        $this->getInfiniteEffects()[strtolower($name)] = $effectInstance;
    }

    public function clear(): void
    {
        $this->infiniteEffects = [];
        parent::clear();
    }
}