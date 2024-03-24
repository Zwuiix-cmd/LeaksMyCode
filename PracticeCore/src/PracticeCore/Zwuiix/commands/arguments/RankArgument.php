<?php

namespace PracticeCore\Zwuiix\commands\arguments;

use pocketmine\command\CommandSender;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\StringEnumArgument;

class RankArgument extends StringEnumArgument
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
        return "rank";
    }
    public function getEnumName(): string
    {
        return "rank";
    }
}