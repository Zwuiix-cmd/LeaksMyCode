<?php

namespace MusuiAntiCheat\Zwuiix\handler;

use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\task\AsyncWebhook;
use pocketmine\thread\NonThreadSafeValue;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

class WebhookHandler
{
    use SingletonTrait;

    public const BAN = 0;
    public const LOGS = 1;
    protected array $webhooks = [];
    public bool $logs = false;
    public bool $ban = false;
    public string $url = "";

    public function __construct()
    {
        $this->load();
    }

    public function load(): void
    {
        $data = Main::getInstance()->getData();
        $this->logs = $data->getNested("discord.logs", false);
        $this->ban = $data->getNested("discord.ban", false);
        $this->url = $data->getNested("discord.webhookLink", "none");
        if($this->ban) {
            $this->webhooks[self::BAN] = $this->readFile(Path::join(Main::getInstance()->getDataFolder(), "webhooks", "ban.json"));
        }
        if($this->logs) {
            $this->webhooks[self::LOGS] = $this->readFile(Path::join(Main::getInstance()->getDataFolder(), "webhooks", "logs.json"));
        }
    }

    /**
     * @param string $path
     * @return bool|string
     */
    public function readFile(string $path): bool|string
    {
        return file_get_contents($path);
    }

    /**
     * @param int $type
     * @return false|string
     */
    public function getWebhook(int $type): bool|string
    {
        if(!isset($this->webhooks[$type])) {
            return false;
        }
        return $this->webhooks[$type];
    }

    /**
     * @param string $url
     * @param string $jsonString
     * @return void
     */
    public function send(string $url, string $jsonString): void
    {
        Main::getInstance()->getServer()->getAsyncPool()->submitTask(new AsyncWebhook(new NonThreadSafeValue($url), new NonThreadSafeValue($jsonString)));
    }
}