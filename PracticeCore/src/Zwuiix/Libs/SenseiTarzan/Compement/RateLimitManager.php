<?php

namespace Zwuiix\Libs\SenseiTarzan\Compement;

use Zwuiix\Main;

class RateLimitManager
{

    private static RateLimitManager $instance;
    private Main $plugin;

    /**
     * @var int[]
     */
    public array $rateLimitPacket = [];

    public function __construct(Main $main)
    {
        self::$instance = $this;
        $this->plugin = $main;
    }


    public function addRateLimitPacket(string $xuid): void
    {
        if (!isset($this->rateLimitPacket[$xuid])) {
            $this->rateLimitPacket[$xuid] = 0;
        }

        $this->rateLimitPacket[$xuid]++;
    }

    public function resetRateLimitPacket(string $xuid): void
    {
        $this->rateLimitPacket[$xuid] = 0;
    }

    public function removeRateLimitPacket(string $xuid): void
    {
        if (isset($this->rateLimitPacket[$xuid])) {
            unset($this->rateLimitPacket[$xuid]);
        }
    }

    public function getRateLimitPacket(string $xuid): int
    {
        return $this->rateLimitPacket[$xuid] ?? 0;
    }

    /**
     * @return RateLimitManager
     */
    public static function getInstance(): RateLimitManager
    {
        return self::$instance;
    }

}