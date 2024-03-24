<?php

namespace PlutooCore\handlers\crate;

use pocketmine\utils\SingletonTrait;

class CrateHandler
{
    use SingletonTrait;

    protected array $crates = array();

    public function __construct()
    {
        self::setInstance($this);
    }

    /**
     * @param Crate $crate
     * @return void
     */
    public function register(Crate $crate): void
    {
        if(isset($this->crates[strtolower($crate->getName())])) return;
        $this->crates[strtolower($crate->getName())] = $crate;
    }

    /**
     * @param string $str
     * @return bool
     */
    public function existCrate(string $str): bool
    {
        return isset($this->crates[$str]);
    }

    /**
     * @param string $str
     * @return Crate|null
     */
    public function getCrateByName(string $str): ?Crate
    {
        return $this->crates[strtolower($str)] ?? null;
    }

    /**
     * @return Crate[]
     */
    public function getAll(): array
    {
        return $this->crates;
    }
}
