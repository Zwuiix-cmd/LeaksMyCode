<?php

namespace MusuiAntiCheat\Zwuiix\session;

class CpsManager
{
    private array $clicks = array();
    protected array $clicksAverage = array();

    public function __construct(
        protected Session $session
    ) {}

    /**
     * @return void
     */
    public function addClick(): void
    {
        array_unshift($this->clicks, microtime(true));
        $this->clicksAverage[] = $this->getClick();
    }

    /**
     * @param bool $average
     * @return float
     */
    public function getClick(bool $average = false): float
    {
        if($average) {
            $initialize = 0;
            for ($i = 0; $i < count($this->clicksAverage); $i ++) {
                $initialize = $initialize + $this->clicksAverage[$i];
            }
            return $initialize === 0 ? 0 : (count($this->clicksAverage) === 0 ? 0 : round($initialize / count($this->clicksAverage)));
        }
        return round(count(array_filter($this->clicks, static function (float $t): bool {
                return (microtime(true) - $t) <= 1.0;
            })) / 1.0, 1);
    }
}