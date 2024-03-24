<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use ReflectionException;

class PacketsE extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "E",
            ModuleManager::generateDefaultData(
                "Allows you to check whether the customer is sending truthful information, or whether he is misrepresenting any information.",
                1,
            ));
    }

    /**
     * @param Session $session
     * @param mixed $packet
     * @param mixed|null $event
     * @return void
     * @throws JsonException
     * @throws ReflectionException
     */
    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof RequestChunkRadiusPacket || $packet instanceof TextPacket || $packet instanceof CommandRequestPacket || $packet instanceof MovePlayerPacket) {
            $data = $session->getPlayer()->getPlayerInfo()->getExtraData();
            $os = match ($data["TitleID"]) {
                "896928775" => DeviceOS::WINDOWS_10,
                "2047319603" => DeviceOS::NINTENDO,
                "1739947436" => DeviceOS::ANDROID,
                "2044456598" => DeviceOS::PLAYSTATION,
                "1828326430" => DeviceOS::XBOX,
                "1810924247" => DeviceOS::IOS,
                default => "Unknown",
            };
            if($os !== $data["DeviceOS"]) {
                $session->flag($this, ["titleId={$os}", "deviceOs={$data["DeviceOS"]}"]);
            }
        }
    }
}