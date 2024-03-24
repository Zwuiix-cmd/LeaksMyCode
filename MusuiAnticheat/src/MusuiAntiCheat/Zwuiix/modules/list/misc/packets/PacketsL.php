<?php

namespace MusuiAntiCheat\Zwuiix\modules\list\misc\packets;

use JsonException;
use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\Server;
use ReflectionException;

class PacketsL extends Module
{
    public function __construct()
    {
        parent::__construct("Packets", "L",
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
        if ($packet instanceof ResourcePackClientResponsePacket) {
            $countPackIds = count($packet->packIds);
            if($countPackIds > count(Server::getInstance()->getResourcePackManager()->getPackIdList())) {
                $session->flag($this, ["type=resource_pack_client_response", "value={$countPackIds}", "ping={$session->getNetwork()->getPing()}ms"]);
            }
        }
    }
}