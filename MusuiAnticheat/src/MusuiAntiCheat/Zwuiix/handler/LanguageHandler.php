<?php

namespace MusuiAntiCheat\Zwuiix\handler;

use MusuiAntiCheat\Zwuiix\utils\Data;
use pocketmine\utils\SingletonTrait;

class LanguageHandler
{
    use SingletonTrait;

    public function __construct(
        protected Data $data
    ) {
        self::setInstance($this);
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param string $message
     * @param array $data
     * @return string
     */
    public function translate(string $message, array $data = []): string
    {
        $value = str_replace("{LINE}", "\n", $this->getData()->get($message, $message));
        foreach ($data as $i => $variable) {
            $value = str_replace("{data[{$i}]}", $variable, $value);
        }

        return "{$value}";
    }
}