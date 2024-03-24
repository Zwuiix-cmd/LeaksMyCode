<?php

namespace MusuiAntiCheat\Zwuiix\command\arguments;

use MusuiAntiCheat\Zwuiix\libs\CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;

class ModuleArgument extends StringEnumArgument
{
    public static array $VALUES = [];
    public function __construct(string $name, bool $optional = false)
    {
        parent::__construct($name, $optional);
    }


    public function getValue(string $string) {
        return self::$VALUES[strtolower($string)] ?? $string;
    }

    public function getEnumValues(): array {
        return array_keys(self::$VALUES);
    }


    public function canParse(string $testString, CommandSender $sender): bool {
        return true;
    }

    public function parse(string $argument, CommandSender $sender): string
    {
        return $this->getValue($argument);
    }

    public function getTypeName(): string
    {
        return "module";
    }
    public function getEnumName(): string
    {
        return "module";
    }
}