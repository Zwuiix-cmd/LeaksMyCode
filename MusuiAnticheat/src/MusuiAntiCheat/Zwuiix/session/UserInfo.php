<?php

namespace MusuiAntiCheat\Zwuiix\session;

use pocketmine\network\mcpe\protocol\types\DeviceOS;

class UserInfo
{
    public const TAG_INPUT_MODE = "CurrentInputMode";
    public const TAG_DEVICE_MODEL = "DeviceModel";
    public const TAG_DEVICE_ID = "DeviceId";
    public const TAG_CLIENT_RANDOM_ID = "ClientRandomId";
    public const TAG_SELF_SIGNED_ID = "SelfSignedId";
    public const TAG_SERVER_ADDRESS = "ServerAddress";
    public const TAG_GAME_VERSION = "GameVersion";

    public function __construct(protected Session $session) {}

    public function getPlayerPlatform(): string
    {
        $data = $this->session->getData();
        if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
            return "Linux";
        }

        return match ($data["DeviceOS"])
        {
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::WINDOWS_10 => "Windows",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo Switch",
            DeviceOS::XBOX => "Xbox",
            default => "Unknown"
        };
    }

    /**
     * @param bool $asString
     * @return string|int
     */
    public function getInputMode(bool $asString = false): string|int
    {
        $mode = $this->session->getData()[self::TAG_INPUT_MODE] ?? 0;
        return $asString ? match($mode) {
            0 => 'Unknown',
            1 => 'Clavier/Souris',
            2 => 'Touch',
            3 => 'Manette'
        } : $mode;
    }

    /**
     * @return string
     */
    public function getDeviceModel(): string
    {
        return $this->session->getData()[self::TAG_DEVICE_MODEL] ?? "Unknown";
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->session->getData()[self::TAG_DEVICE_ID] ?? "Unknown";
    }

    /**
     * @return int
     */
    public function getClientRandomId(): int
    {
        return $this->session->getData()[self::TAG_CLIENT_RANDOM_ID] ?? 0;
    }

    public function getSelfSignedId(): string
    {
        return $this->session->getData()[self::TAG_SELF_SIGNED_ID] ?? "Unknown";
    }

    public function getServerAddress(): string
    {
        return $this->session->getData()[self::TAG_SERVER_ADDRESS] ?? "Unknown";
    }

    public function getGameVersion(): string
    {
        return $this->session->getData()[self::TAG_GAME_VERSION] ?? "Unknown";
    }
}