<?php

namespace PracticeCore\Zwuiix\handler;

use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\commands\arguments\RankArgument;
use PracticeCore\Zwuiix\rank\Rank;

class RankHandler
{
    use SingletonTrait;

    /*** @var Rank[] */
    protected array $ranks = array();
    protected ?Rank $default = null;

    /**
     * @param Rank $rank
     * @return void
     */
    public function register(Rank $rank): void
    {
        if(isset($this->ranks[strtolower($rank->getName())])) return;
        $this->ranks[strtolower($rank->getName())] = $rank;
        RankArgument::$VALUES[$rank->getName()] = $rank->getName();
        if($rank->isDefault()) $this->default = $rank;
    }

    /**
     * @param string $name
     * @return Rank|null
     */
    public function getRankByName(string $name): ?Rank
    {
        return $this->ranks[strtolower($name)] ?? null;
    }

    /**
     * @return Rank|null
     */
    public function getDefaultRank(): ?Rank
    {
        return $this->default;
    }

    /**
     * @return Rank[]
     */
    public function getAll(): array
    {
        return $this->ranks;
    }
}