<?php

namespace Zwuiix\Listener\Packet;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\Server;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Utils\PacketUtils;

class DataPacketReceive implements Listener
{

    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @return bool
     */
    public function dataPacket(DataPacketReceiveEvent $event): bool
    {
        $packet=$event->getPacket();
        $origin = $event->getOrigin();
        $player=$event->getOrigin()->getPlayer();
        if(!$player instanceof User)return false;
        if($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData && $packet->trData->getActionType() == UseItemOnEntityTransactionData::ACTION_ATTACK){
            if($player->isSpectator()) {
                $event->cancel();
                return false;
            }
        }elseif($packet instanceof LevelSoundEventPacket){
            if($packet::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $packet->sound === LevelSoundEvent::ATTACK_NODAMAGE){
                $player->broadcastAnimation(new ArmSwingAnimation($player), $player->getViewers());
            }
            if($packet::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $packet->sound === LevelSoundEvent::ATTACK_NODAMAGE or $packet->sound === LevelSoundEvent::ATTACK_STRONG){
                if($player->getCps() >= 19){
                    $event->cancel();
                    return false;
                }
                $player->addCps();
            }
        }elseif ($packet::NETWORK_ID === EmotePacket::NETWORK_ID and $packet instanceof EmotePacket) {
            $event->cancel();
        }
        if($packet instanceof PlayerAuthInputPacket){
            $location = Location::fromObject($packet->getPosition()->subtract(0, 1.62, 0), $player->getWorld(), $packet->getYaw(), $packet->getPitch());

            $player->previousYaw = $player->currentYaw;
            $player->currentYaw = $location->yaw;
            $player->currentYawDelta = abs($player->currentYaw - $player->previousYaw);
            if ($player->currentYawDelta > 180) {
                $player->currentYawDelta = 360 - $player->currentYawDelta;
            }

            $player->tick();
        }
        return true;
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     * @priority HIGHEST
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $player = ($networkSession = $event->getOrigin())->getPlayer();
        $packet = $event->getPacket();

        if($packet instanceof LoginPacket) {
            $server=Server::getInstance();
            $extraData = PacketUtils::fetchAuthData($packet->chainDataJwt);

            if($server->getNetwork()->getValidConnectionCount() > $server->getMaxPlayers()){
                $networkSession->sendDataPacket(DisconnectPacket::create("§cDésolé, le serveur est actuellement plein!"));
                return;
            }
            if(!$server->isWhitelisted($extraData->displayName)){
                $networkSession->sendDataPacket(DisconnectPacket::create("§cDésolé, le serveur est actuellement en maintenance!"));
                return;
            }
            if($server->getNameBans()->isBanned($extraData->displayName) || $server->getIPBans()->isBanned($networkSession->getIp())){
                $networkSession->sendDataPacket(DisconnectPacket::create("§cDésolé, vous êtes banni(e) définitivement du serveur!"));
                return;
            }
        }

        if(!$player instanceof User)return;
        if(!$packet instanceof PlayerAuthInputPacket) return;

        $inputFlags = $packet->getInputFlags();
        if($player->lastAttack === null) return;
        if($player->lastPlayerAuthInputFlags === null){
            $player->lastPlayerAuthInputFlags=$inputFlags;
            return;
        }
        if($player->lastSprint === null){
            $player->lastSprint=time();
            return;
        }

        $sprinting = $this->resolveOnOffInputFlags($inputFlags);
        if($inputFlags !== $player->lastPlayerAuthInputFlags){

            $lastSprint=$player->lastSprint;
            $lastAttack=$player->lastAttack;

            $player->lastPlayerAuthInputFlags = $inputFlags;
            if($sprinting === null) return;

            $sprinting = $this->resolveOnOffInputFlags($inputFlags);
            if($sprinting && ($lastSprint - time() >= 0) && ($lastAttack - time() >= 0)) $player->addWTAPHit();

            $player->lastSprint=time();
        }
    }

    private function resolveOnOffInputFlags(int $inputFlags) : ?bool
    {
        $enabled = ($inputFlags & (1 << PlayerAuthInputFlags::START_SPRINTING)) !== 0;
        $disabled = ($inputFlags & (1 << PlayerAuthInputFlags::STOP_SPRINTING)) !== 0;
        if($enabled !== $disabled){
            return $enabled;
        }
        return null;
    }

    public function onDamageEvent(EntityDamageEvent $event)
    {
        if(!$event instanceof EntityDamageByEntityEvent)return;
        $damager=$event->getDamager();
        $entity=$event->getEntity();
        if(!$damager instanceof User) return;
        if(!$entity instanceof User) return;

        $entity->lastAttackTime=time();
        $damager->lastAttack=time();
        $damager->addAllHit();
    }
}