<?php

declare(strict_types=1);

namespace MyIpScript;

use pocketmine\plugin\PluginBase;

/**
 * @name MyIpScript
 * @author Zwuiix
 * @main MyIpScript\MyIpScript
 * @api 5.0.0
 * @version 1.0.0
 */
class MyIpScript extends PluginBase
{
    /**
     * @return void
     */
    protected function onLoad(): void
    {
        $this->getLogger()->alert("Your IP: {$this->getAddress()}");
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        $externalContent = file_get_contents('http://checkip.dyndns.com/');
        preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
        return $m[1];
    }
}