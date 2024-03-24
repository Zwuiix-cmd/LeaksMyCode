<?php

namespace Zwuiix\Libs\SenseiTarzan\Listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\lang\KnownTranslationKeys;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackStackEntry;
use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackType;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\Server;
use Zwuiix\Libs\SenseiTarzan\Compement\PacketSend;
use Zwuiix\Libs\SenseiTarzan\Compement\ResourcePackManager;

class DataPacketEvent implements Listener
{

    public function onDataSend(DataPacketSendEvent $event){
        $packets = $event->getPackets();
        foreach ($packets as $packet){
            if ($packet instanceof ResourcePacksInfoPacket){
                $packet->resourcePackEntries = array_merge($packet->resourcePackEntries, ResourcePackManager::getInstance()->getResourcePackEntries());
            }
        }
    }

    public function onDataReceive(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        $session = $event->getOrigin();
        if ($packet instanceof ResourcePackClientResponsePacket){
            switch ($packet->status){
                case ResourcePackClientResponsePacket::STATUS_SEND_PACKS:
                    $event->cancel();
                    ResourcePackManager::$packSend[$session->getDisplayName()] = $send = new PacketSend($session);
                    foreach($packet->packIds as $uuid){
                        //dirty hack for mojang's dirty hack for versions
                        $splitPos = strpos($uuid, "_");
                        if($splitPos !== false){
                            $uuid = substr($uuid, 0, $splitPos);
                        }
                        $pack = Server::getInstance()->getResourcePackManager()->getPackById($uuid) ?? ResourcePackManager::getInstance()->getPackById($uuid);

                        if(!($pack instanceof ResourcePack)){
                            //Client requested a resource pack but we don't have it available on the server
                            $this->disconnectWithError($session,"Unknown pack $uuid requested, available packs: " . implode(", ",  array_merge(Server::getInstance()->getResourcePackManager()->getPackIdList(),ResourcePackManager::getInstance()->getPackIdList())));
                            return false;
                        }

                        $session->sendDataPacket($pk = ResourcePackDataInfoPacket::create(
                            $pack->getPackId(),
                            ResourcePackManager::$PACK_CHUNK_SIZE,
                            (int) ceil($pack->getPackSize() / ResourcePackManager::$PACK_CHUNK_SIZE),
                            $pack->getPackSize(),
                            $pack->getSha256(),
                            false,
                            ResourcePackType::RESOURCES //TODO: this might be an addon (not behaviour pack), needed to properly support client-side custom items
                        ));

                        for ($i = 0; $i < $pk->chunkCount; $i++) {
                            $send->addPacket(ResourcePackChunkDataPacket::create($pack->getPackId(), $i, $offset = ResourcePackManager::$PACK_CHUNK_SIZE * $i, $pack->getPackChunk($offset, ResourcePackManager::$PACK_CHUNK_SIZE)));
                        }
                    }
                    $session->getLogger()->debug("Player requested download of " . count($packet->packIds) . " resource packs");

                    break;
                case ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS:
                    $event->cancel();
                    $stack = [];
                    foreach (Server::getInstance()->getResourcePackManager()->getResourceStack() as $pack){
                        $stack[] = new ResourcePackStackEntry($pack->getPackId(), $pack->getPackVersion(), "");
                    }

                    foreach (ResourcePackManager::getInstance()->getResourceStack() as $pack){
                        $stack[] = new ResourcePackStackEntry($pack->getPackId(), $pack->getPackVersion(), "");
                    }
                    //we support chemistry blocks by default, the client should already have this installed
                    $stack[] = new ResourcePackStackEntry("0fba4063-dba1-4281-9b89-ff9390653530", "1.0.0", "");

                    //we don't force here, because it doesn't have user-facing effects
                    //but it does have an annoying side-effect when true: it makes
                    //the client remove its own non-server-supplied resource packs.
                    $session->sendDataPacket(ResourcePackStackPacket::create($stack, [], false, ProtocolInfo::MINECRAFT_VERSION_NETWORK, new Experiments([], false)));
                    $session->getLogger()->debug("Applying resource pack stack");
                    break;
            }
        }elseif ($packet instanceof ResourcePackChunkRequestPacket){
            $event->cancel();
        }
    }



    private function disconnectWithError(NetworkSession $session, string $error) : void{
        $session->getLogger()->error("Error downloading resource packs: " . $error);
        $session->disconnect(KnownTranslationKeys::DISCONNECTIONSCREEN_RESOURCEPACK);
    }

}