<?php

namespace MusuiAntiCheat\Zwuiix\modules\list;

use MusuiAntiCheat\Zwuiix\modules\Module;
use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\session\SessionManager;
use MusuiAntiCheat\Zwuiix\utils\MovementConstants;
use pocketmine\block\Cobweb;
use pocketmine\block\Ladder;
use pocketmine\block\Liquid;
use pocketmine\block\Vine;
use pocketmine\entity\Location;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\SetActorMotionPacket;
use pocketmine\network\mcpe\protocol\TickSyncPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\player\Player;

class ModuleInitializer extends Module
{
    public function __construct()
    {
        parent::__construct("ModuleInitializer", "Z");
    }

    public function callInbound(Session $session, mixed $packet, mixed $event = null): void
    {
        if($packet instanceof PlayerAuthInputPacket) {
            $location = Location::fromObject($packet->getPosition()->subtract(0, 1.62, 0), $session->getPlayer()->getWorld(), $packet->getYaw(), $packet->getPitch());
            $floor = $location->floor();
            $session->isInLoadedChunk = $session->getPlayer()->getWorld()->isChunkLoaded($floor->x >> 4, $floor->z >> 4);

            $session->currentTick = $packet->getTick();
            $session->lastLocation = clone $session->currentLocation;
            $session->currentLocation = $location;
            $session->lastMoveDelta = $session->currentMoveDelta;
            $session->currentMoveDelta = $session->currentLocation->subtractVector($session->lastLocation)->asVector3();
            $session->previousYaw = $session->currentYaw;
            $session->previousPitch = $session->currentPitch;
            $session->currentYaw = $location->yaw;
            $session->currentPitch = $location->pitch;
            $session->lastYawDelta = $session->currentYawDelta;
            $session->lastPitchDelta = $session->currentPitchDelta;
            $session->currentYawDelta = abs($session->currentYaw - $session->previousYaw);
            $session->currentPitchDelta = abs($session->currentPitch - $session->previousPitch);
            if ($session->currentYawDelta > 180) {
                $session->currentYawDelta = 360 - $session->currentYawDelta;
            }

            $session->isInVoid = $location->y <= -35;
            if ($session->getPlayer()->isFlying()) {
                $session->flyingTicks = 0;
            } else $session->flyingTicks++;

            if ($session->getPlayer()->isGliding()) {
                $session->glidingTicks = 0;
            } else $session->glidingTicks++;

            if($packet->hasFlag(PlayerAuthInputFlags::START_JUMPING)){
                $session->lastJumpTicks = 0;
            } else $session->lastJumpTicks++;

            if($session->getPlayer()->onGround) {
                $session->lastGroundDelta = $packet->getDelta();
                $session->lastGroundTick = $packet->getTick();
                $session->lastGroundPosition = clone $location;
            }

            if(!$session->getPlayer()->hasNoClientPredictions()) {
                $session->lastLocationNoClientPredictions = clone $location;
            }
            if(!$session->isInsideOfSolid()) {
                $session->lastNoSuffocateLocation = clone $location;
            }

            if($session->currentMoveDelta->lengthSquared() > 0.0009) {
                $liquids = 0;
                $cobweb = 0;
                $climb = 0;
                foreach($session->getPlayer()->getWorld()->getCollisionBlocks($session->getPlayer()->getBoundingBox()) as $block){
                    if($block instanceof Liquid){
                        $liquids++;
                    } elseif($block instanceof Cobweb){
                        $cobweb++;
                    } elseif($block instanceof Ladder || $block instanceof Vine){
                        $climb++;
                    }
                }
                if($liquids > 0){
                    $session->liquidTicks = 0;
                } else $session->liquidTicks++;
                if($cobweb > 0){
                    $session->cobwebTicks = 0;
                } else $session->cobwebTicks++;
                if($climb > 0){
                    $session->climbableTicks = 0;
                } else $session->climbableTicks++;
            }

            if($session->inventoryClose) {
                $session->inventoryLastCloseTicks++;
            } else $session->inventoryLastCloseTicks = 0;

            $session->lastAttackTicks++;
            $session->teleportTicks++;
            $session->lastDeathTicks++;
            $session->motionTicks++;

            $session->moveForward = $packet->getMoveVecZ() * 0.98;
            $session->moveStrafe = $packet->getMoveVecX() * 0.98;

            $session->pressedKeys = [];
            if($packet->getMoveVecZ() > 0){
                $session->pressedKeys[] = 'W';
            } elseif($packet->getMoveVecZ() < 0){
                $session->pressedKeys[] = 'S';
            }
            if($packet->getMoveVecX() > 0){
                $session->pressedKeys[] = 'A';
            } elseif($packet->getMoveVecX() < 0){
                $session->pressedKeys[] = 'D';
            }

            if($packet->hasFlag(PlayerAuthInputFlags::MISSED_SWING) && $session->lastClickTicks >= 1) {
                $session->getCpsManager()->addClick();
            }

            $inputFlags = $packet->getInputFlags();
            if($inputFlags !== $session->lastPlayerAuthInputFlags) {
                $session->lastPlayerAuthInputFlags = $inputFlags;

                $flying = MovementConstants::resolveOnOffInputFlags($inputFlags, PlayerAuthInputFlags::START_FLYING, PlayerAuthInputFlags::STOP_FLYING);
                $sprinting = MovementConstants::resolveOnOffInputFlags($inputFlags, PlayerAuthInputFlags::START_SPRINTING, PlayerAuthInputFlags::STOP_SPRINTING);
                $sneaking = MovementConstants::resolveOnOffInputFlags($inputFlags, PlayerAuthInputFlags::START_SNEAKING, PlayerAuthInputFlags::START_SNEAKING);
                $session->isSprinting = is_null($sprinting) ? false : $sprinting;
                $session->isSneaking = is_null($sneaking) ? false : $sneaking;
            }

            $session->isSprinting ? $session->jumpMovementFactor = MovementConstants::JUMP_MOVE_SPRINT : $session->jumpMovementFactor = MovementConstants::JUMP_MOVE_NORMAL;
            $session->getNetwork()->sendDataPacket(LevelEventPacket::create(LevelEvent::SET_GAME_SPEED, 0, new Vector3(1, 0, 0)));
        } elseif ($packet instanceof InventoryTransactionPacket) {
            $trData = $packet->trData;
            if ($trData instanceof UseItemOnEntityTransactionData) {
                $session->lastTarget = $session->target;
                $session->target = $trData->getActorRuntimeId();
                $session->attackTick = $session->currentTick;
                $session->attackPos = $trData->getPlayerPosition();
                if($trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK) {
                    $session->getCpsManager()->addClick();
                    $session->lastClickTicks = 0;
                }
            }
        }elseif($packet instanceof LevelSoundEventPacket) {
            if(($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE or $packet->sound === LevelSoundEvent::ATTACK_STRONG) && $session->lastClickTicks >= 1){
                $session->getCpsManager()->addClick();
            }
        }elseif ($packet instanceof AnimatePacket) {
            $session->getCpsManager()->addClick();
        }
    }
}