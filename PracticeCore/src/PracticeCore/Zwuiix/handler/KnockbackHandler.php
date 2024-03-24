<?php

namespace PracticeCore\Zwuiix\handler;

use JsonException;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\utils\Data;

class KnockbackHandler
{
    use SingletonTrait;

    protected Vector2 $knockback;
    protected Vector3 $heightLimiter;
    protected int $attackCooldown;

    protected Data $data;

    /**
     * @throws JsonException
     */
    public function __construct()
    {
        $this->data = new Data(PracticeCore::getInstance()->getPlugin()->getDataFolder() . "/knockback.yml", Data::YAML, [
            "knockback" => [
                "xz" => 0.4,
                "y" => 0.4,
            ],
            "heightlimiter" => [
                "maxheightreduce" => 1.17,
                "maxdistance" => 3,
                "all" => 0.026
            ],
            "attackCooldown" => 10
        ]);

        $this->knockback = new Vector2($this->data->getNested("knockback.x", 0.4), $this->data->getNested("knockback.z", 0.4));
        $this->heightLimiter = new Vector3(
            $this->data->getNested("heightlimiter.maxheightreduce", 1.17),
            $this->data->getNested("heightlimiter.all", 0.026),
            $this->data->getNested("heightlimiter.maxdistance", 3)
        );
        $this->attackCooldown = $this->data->get("attackCooldown", 10);
    }

    /**
     * @return Vector2
     */
    public function getKnockback(): Vector2
    {
        return $this->knockback;
    }

    /**
     * @param Vector2 $knockback
     * @return void
     */
    public function setKnockback(Vector2 $knockback): void
    {
        $this->knockback = $knockback;
    }

    /**
     * @return Vector3
     */
    public function getHeightLimiter(): Vector3
    {
        return $this->heightLimiter;
    }

    /**
     * @param Vector3 $heightLimiter
     */
    public function setHeightLimiter(Vector3 $heightLimiter): void
    {
        $this->heightLimiter = $heightLimiter;
    }

    /**
     * @return int
     */
    public function getAttackCooldown(): int
    {
        return $this->attackCooldown;
    }

    /**
     * @param int $attackCooldown
     */
    public function setAttackCooldown(int $attackCooldown): void
    {
        $this->attackCooldown = $attackCooldown;
    }

    /**
     * @throws JsonException
     */
    public function save(): void
    {
        $this->data->setAll([
            "knockback" => [
                "xz" => $this->getKnockback()->getX(),
                "y" => $this->getKnockback()->getY(),
            ],
            "heightlimiter" => [
                "maxheightreduce" => $this->getHeightLimiter()->getX(),
                "maxdistance" => $this->getHeightLimiter()->getZ(),
                "all" => $this->getHeightLimiter()->getY()
            ],
            "attackCooldown" => $this->getAttackCooldown()
        ]);
        $this->data->save();
    }
}