<?php

namespace MusuiAntiCheat\Zwuiix\modules;

use JsonException;
use MusuiAntiCheat\Zwuiix\libs\Zwuiix\AutoLoader\Loader;
use MusuiAntiCheat\Zwuiix\libs\Zwuiix\AutoLoader\PathScanner;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\Data;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\timings\Timings;
use pocketmine\timings\TimingsHandler;
use Symfony\Component\Filesystem\Path;

abstract class Module
{
    protected Data $data;

    public ?TimingsHandler $timings = null;

    protected bool $detected = false;
    public array $buffer = array();

    /**
     * @param string $name
     * @param string $type
     * @param array $defaultData
     * @throws JsonException
     */
    public function __construct(
        protected string $name,
        protected string $type,
        protected array $defaultData = []
    ) {
        $this->timings = new TimingsHandler("Module {$this->name}{$this->type}");
        Timings::$packetReceiveTimingMap["{$this->name}{$this->type}"] = new TimingsHandler("Module {$this->name}{$this->type}", Timings::$playerNetworkReceive, group: Timings::GROUP_BREAKDOWN);
        $path = Main::getInstance()->getDataFolder();

        $scan = PathScanner::scanDirectory(Path::join(__DIR__, "list"), ["php"]);
        $find = "";
        foreach ($scan as $file) {
            $v = explode("\\list\\", Loader::getInstance()->getUsePathWithPathFile($file))[1];
            if(str_contains($v, "{$this->name}{$this->type}")) {
                $find = $v;
                break;
            }
        }

        if($find !== "") {
            $dir = explode("{$this->name}{$this->type}", $find);

            @mkdir(Path::join($path . "\modules\\" . $dir[0]), recursive: true);
            $this->data = new Data(Path::join($path . "\modules\\" . $find  . ".yml"), Data::YAML, $defaultData);
        } else $this->data = new Data(Path::join($path . "\modules\\{$this->name}{$this->type}.yml"), Data::YAML, $this->defaultData);

        foreach ($this->defaultData as $name => $value) {
            if(!$this->data->exists($name)) {
                $this->data->define($name, $value);
            }
        }

        $this->data->save();
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "{$this->options("description", "Unknown")}";
    }

    /**
     * @param bool $status
     * @return void
     */
    public function detect(bool $status = true): void
    {
        $this->detected = $status;
    }

    /**
     * @return bool
     */
    public function isDetected(): bool
    {
        return $this->detected;
    }

    /**
     * @return TimingsHandler
     */
    public function getTimings() : TimingsHandler
    {
        return $this->timings;
    }

    /**
     * @return float
     */
    public function getMaxVL(): float
    {
        return floatval($this->options("maxVL", 100));
    }

    /**
     * @return bool|mixed
     */
    public function isEnabled(): mixed
    {
        return $this->options("enabled", true);
    }

    /**
     * @param bool $value
     * @return void
     * @throws JsonException
     */
    public function setEnabled(bool $value = true): void
    {
        $this->define("enabled", $value);
        $this->getData()->save();
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param string $value
     * @param mixed $default
     * @return bool|mixed
     */
    public function options(string $value, mixed $default): mixed
    {
        return $this->getData()->options($value, $default);
    }

    /**
     * @param string $value
     * @param mixed $push
     */
    public function define(string $value, mixed $push) : void{
        $this->getData()->define($value, $push);
    }

    /**
     * @param Session $session
     * @param mixed $packet
     * @param mixed|null $event
     * @return void
     */
    abstract public function callInbound(Session $session, mixed $packet, mixed $event = null): void;

    public function callOutbound(Session $session, ClientboundPacket $packet, DataPacketSendEvent $event) {}
}