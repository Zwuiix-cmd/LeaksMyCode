<?php

namespace MusuiAntiCheat\Zwuiix\handler;

use JsonException;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\Data;
use pocketmine\utils\SingletonTrait;

class AliasesHandler
{
    public const ADDRESS = "address";
    public const UUID = "uuid";
    public const XUID = "xuid";
    public const DEVICEID = "deviceid";

    use SingletonTrait;

    protected array $cache = [];

    public function __construct(protected Data $data)
    {
        self::setInstance($this);
        $this->cache = $this->data->getAll();

        foreach ([self::ADDRESS, self::UUID, self::XUID, self::DEVICEID] as $item) {
            if(!isset($this->cache[$item])) {
                $this->cache[$item] = [];
            }
        }
    }

    /**
     * @return array|mixed[]
     */
    public function getData(): array
    {
        return $this->cache;
    }

    /**
     * @param Session $session
     * @return array
     */
    public function getAlt(Session $session): array
    {
        return [
            self::ADDRESS => $this->getAccountWithInfo(self::ADDRESS, md5($session->getNetwork()->getIp())),
            self::UUID => $this->getAccountWithInfo(self::UUID, md5($session->getNetwork()->getPlayerInfo()->getUuid())),
            self::XUID => $this->getAccountWithInfo(self::XUID, md5($session->getPlayer()->getXuid())),
            self::DEVICEID => $this->getAccountWithInfo(self::DEVICEID, md5($session->getUserInfo()->getDeviceId())),
        ];
    }

    /**
     * @param Session $session
     * @return array
     */
    public function syncAllAlt(Session $session): array
    {
        $alts = $this->getAlt($session);
        return array_unique(array_merge($alts[self::ADDRESS], $alts[self::XUID], $alts[self::UUID], $alts[self::DEVICEID]));
    }

    public function altFormat(string $separator, array $value): string
    {
        return implode($separator, $value);
    }

    /**
     * @param Session $session
     * @return void
     */
    public function initializeSession(Session $session): void
    {
        if(!$session->getPlayer()->isConnected()) return;
        $name = $session->getPlayer()->getName();

        $this->registerInfo(self::ADDRESS, md5($session->getNetwork()->getIp()), $name);
        $this->registerInfo(self::UUID, md5($session->getNetwork()->getPlayerInfo()->getUuid()), $name);
        $this->registerInfo(self::XUID, md5($session->getPlayer()->getXuid()), $name);
        $this->registerInfo(self::DEVICEID, md5($session->getUserInfo()->getDeviceId()), $name);
    }

    /**
     * @param string $type
     * @param string $value
     * @return array
     */
    public function getAccountWithInfo(string $type, string $value): array
    {
        if (!isset($this->cache[$type][$value])) {
            $this->cache[$type][$value] = [];
        }

        return $this->cache[$type][$value];
    }

    /**
     * @param string $type
     * @param string $value
     * @param string $username
     * @return void
     */
    public function registerInfo(string $type, string $value, string $username): void
    {
        if (!isset($this->cache[$type][$value])) {
            $this->cache[$type][$value] = [];
        }

        if(in_array($username, $this->cache[$type][$value])) {
            return;
        }

        $this->cache[$type][$value][] = $username;
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function save(): void
    {
        $this->data->setAll($this->cache);
        $this->data->save();
    }
}