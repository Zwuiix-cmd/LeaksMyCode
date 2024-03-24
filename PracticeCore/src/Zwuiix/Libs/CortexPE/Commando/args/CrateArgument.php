<?php

namespace Zwuiix\Libs\CortexPE\Commando\args;

use pocketmine\command\CommandSender;
use Zwuiix\Handler\Crate;
use Zwuiix\Handler\Crates;

class CrateArgument extends StringEnumArgument
{
    public static array $VALUES = array();

    public function __construct(string $name, bool $optional = false)
    {
        foreach (Crates::getInstance()->getAll() as $crate){
            self::$VALUES[strtolower($crate->getName())]=$crate->getName();
        }
        parent::__construct($name, $optional);
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function getValue(string $string): mixed
    {
        return self::$VALUES[strtolower($string)];
    }

    public function getEnumValues(): array
    {
        return array_keys(self::$VALUES);
    }

    public function parse(string $argument, CommandSender $sender): ?Crate
    {
        return Crates::getInstance()->getCrateByName($this->getValue($argument));
    }

    public function getTypeName(): string
    {
        return "crate";
    }

    public function getEnumName(): string
    {
        return "crate";
    }
}