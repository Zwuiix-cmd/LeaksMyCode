<?php

namespace PracticeCore\Zwuiix\handler;

use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\ffa\FFA;

class FFAHandler
{
    use SingletonTrait;

    /*** @var FFA[] */
    protected array $ffa = array();

    /**
     * @param FFA $FFA
     * @return void
     */
    public function register(FFA $FFA): void
    {
        $name = strtolower($FFA->getName());
        if(isset($this->ffa[$name])) return;
        $this->ffa[$name] = $FFA;
    }

    /**
     * @param string $name
     * @return FFA|null
     */
    public function getFFAByName(string $name): ?FFA
    {
        return $this->ffa[strtolower($name)] ?? null;
    }

    /**
     * @param int $id
     * @return mixed|FFA
     */
    public function getFFAById(int $id): mixed
    {
        $array = array();
        foreach ($this->getAll() as $FFA) $array[] = $FFA;
        return $array[$id];
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->ffa;
    }
}