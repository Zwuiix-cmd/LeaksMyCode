<?php

namespace PlutooCore\handlers;

use JsonException;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class OptionsHandler
{
    use SingletonTrait;

    protected Config $config;

    public function __construct()
    {
        self::setInstance($this);
        $this->config = new Config(Path::join(\MusuiEssentials::getInstance()->getDataFolder(), "options.db"), Config::JSON);
    }

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     */
    public function get(string $name, string $type, mixed $default = null): mixed
    {
        return $this->config->getNested(strtolower("{$name}.{$type}"), $default);
    }

    /**
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @return void
     */
    public function set(string $name, string $type, mixed $value): void
    {
        $this->config->setNested(strtolower("{$name}.{$type}"), $value);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function save(): void
    {
        $this->config->save();
    }
}