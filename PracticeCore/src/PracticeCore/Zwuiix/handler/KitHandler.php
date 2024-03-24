<?php

namespace PracticeCore\Zwuiix\handler;

use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\kit\Kit;

class KitHandler
{
    use SingletonTrait;

    /*** @var Kit[] */
    protected array $kits = array();

    public function register(Kit $kit): void
    {
        $name = strtolower($kit->getName());
        if(isset($this->kits[$name])) return;
        $this->kits[$name] = $kit;
    }

    /**
     * @param string $name
     * @return Kit|null
     */
    public function getKitByName(string $name): ?Kit
    {
        return $this->kits[strtolower($name)] ?? null;
    }

    /**
     * @return Kit[]
     */
    public function getAll(): array
    {
        return $this->kits;
    }
}