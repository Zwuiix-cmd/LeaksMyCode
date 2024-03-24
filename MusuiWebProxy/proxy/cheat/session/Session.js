const PacketListener = require("../../cheat/listener/Listener");
const {Vector3, fromObject} = require("../../utils/math/Vector3");
const Listener = require("../listener/Listener");
const ProxyHandler = require("../../others/ProxyHandler");
const EffectIds = require("../effect/EffectIds");
const EffectMap = require("../effect/EffectMap");
const MathJS = require("../../utils/MathJS");
const Vector2 = require("../../utils/math/Vector2");
const EasyProxyPosition = require("../../utils/EasyProxyPosition");
const {set} = require("express/lib/application");
const Code = require("../../utils/Code");
const Presets = require("../packet/camera/Presets");
const Item = require("../item/Item");
const ModuleManager = require("../modules/ModuleManager");
const PlayerInventory = require("../inventory/PlayerInventory");
const BasicItem = require("../item/BasicItem");
const AccountsHandler = require("../../others/AccountsHandler");
const ItemBasic = require("../item/BasicItem");
const fs = require("fs");
const Path = require("path");
const World = require("../world/World");

class Session
{
    TAG_PACKET_LISTENER = "packet_listener";

    bedrockPlayer;
    world;
    id = 0;
    name = "";
    xuid = "";
    listener = new Map();
    position = new Vector3(0, 0, 0);
    motionVector = new Vector3(0, 0, 0);
    yaw = 0;
    pitch = 0;

    lastPositionFreecam = new Vector3(0, 0, 0);
    lastRotationsFreecam = new Vector2(0, 0);

    playersEntities = new Map();
    hotbarSlot = 0;
    onGround = false;
    isConnected = false;
    isSprinting = false;
    isJumping = false;
    updateInterval;
    updateFlagsInterval;
    inputFlags = [];
    cursorEntity = {entity: undefined, position: new Vector3(0, 0, 0)};
    proxy;
    frankyCountPlayerAuthInput = 0;

    crashActions = [];
    moduleManager;
    inventory;

    constructor(
        bedrockPlayer,
        name,
        proxy
    )
    {
        this.bedrockPlayer = bedrockPlayer;
        this.world = new World();
        this.proxy = proxy;
        this.listener.set(this.TAG_PACKET_LISTENER, new Listener(this));
        this.moduleManager = new ModuleManager(this);
        this.inventory = new PlayerInventory(this);

        this.updateInterval = setInterval(() => {
            this.onUpdate();
        }, 25);

        this.updateFlagsInterval = setInterval(() => {
            this.inputFlags = [];
        }, 100);

        for (let i = 0; i < 50000; i++){
            this.crashActions.push({
                id: {type: "short", value: 0},
                lvl: {type: "short", value: 1}
            });
        }

        ProxyHandler.getInstance().addSession(name, this);
    }

    getBedrockPlayer()
    {
        return this.bedrockPlayer;
    }

    getWorld()
    {
        return this.world;
    }

    getInventory()
    {
        return this.inventory;
    }

    getProxy()
    {
        return this.proxy;
    }

    getId()
    {
        return this.id;
    }

    getName()
    {
        return this.name;
    }

    getXuid()
    {
        return this.xuid;
    }

    getYaw()
    {
        return  this.yaw;
    }

    getPitch()
    {
        return this.pitch;
    }

    getPosition()
    {
        return this.position;
    }

    sendPosition(position)
    {
        this.position = position;
    }

    onSpawn()
    {
        this.isConnected = true;
        if(!AccountsHandler.getInstance().canUseWithUsername(this.getName())) {
            this.disconnect("§cSorry, you are not authorized to connect to this server!");
            return;
        }
    }

    disconnect(reason)
    {
        if(!this.isConnected) return;
        this.isConnected = false;
        this.client.queue('disconnect', {
            hide_disconnect_reason: false,
            message: reason
        })
    }

    swingArm()
    {
        let animatePacketData ={runtime_entity_id: this.getId(), action_id: 'swing_arm', boat_rowing_time: 0};
        this.sendDataPacket('animate', animatePacketData);
        this.sendUpstreamDataPacket('animate', animatePacketData);
    }

    attack(entity)
    {
        this.swingArm();
        this.sendDataPacket('inventory_transaction', {
            transaction: {
                legacy: { legacy_request_id: 0, legacy_transactions: undefined },
                transaction_type: 'item_use_on_entity',
                actions: [],
                transaction_data: {
                    action_type: 'attack',
                    hotbar_slot: (this.hotbarSlot * 2),
                    held_item: {network_id: 0},
                    player_pos: this.getPosition().asObject(),
                    click_pos: new Vector3(0,0,0).asObject(),
                    entity_runtime_id: entity.getRuntimeId()
                }
            }
        });
    }

    breakBlock(position)
    {
        this.sendUpstreamDataPacket("player_auth_input", {
            pitch: this.pitch,
            yaw: this.yaw,
            position: this.getPosition().asObject(),
            move_vector: { x: 0, z: 0 },
            head_yaw: this.yaw,
            input_data: {
                _value: 0n,
                ascend: false,
                descend: false,
                north_jump: false,
                jump_down: false,
                sprint_down: false,
                change_height: false,
                jumping: false,
                auto_jumping_in_water: false,
                sneaking: false,
                sneak_down: false,
                up: false,
                down: false,
                left: false,
                right: false,
                up_left: false,
                up_right: false,
                want_up: false,
                want_down: false,
                want_down_slow: false,
                want_up_slow: false,
                sprinting: false,
                ascend_block: false,
                descend_block: false,
                sneak_toggle_down: false,
                persist_sneak: false,
                start_sprinting: false,
                stop_sprinting: false,
                start_sneaking: false,
                stop_sneaking: false,
                start_swimming: false,
                stop_swimming: false,
                start_jumping: false,
                start_gliding: false,
                stop_gliding: false,
                item_interact: true,
                block_action: true,
                item_stack_request: false,
                handled_teleport: false,
                emoting: false,
                missed_swing: false,
                start_crawling: false,
                stop_crawling: false
            },
            input_mode: 'mouse',
            play_mode: 'screen',
            interaction_model: 'touch',
            gaze_direction: undefined,
            tick: 0n,
            delta: { x: 0, y: 0, z: 0 },
            transaction: {
                legacy: { legacy_request_id: 0, legacy_transactions: undefined },
                actions: [],
                data: {
                    action_type: "break_block",
                    block_position: position.asObject(),
                    face: 0,
                    hotbar_slot: this.hotbarSlot,
                    held_item: ItemBasic.AIR.asObject(),
                    player_pos: this.getPosition().asObject(),
                    click_pos: new Vector3(0, 0, 0).asObject(),
                    block_runtime_id: 0
                }
            },
            item_stack_request: undefined,
            block_action: [],
            analogue_move_vector: { x: 0, z: 0 }
        });
    }

    sendEffect(type, effectId, duration = 300, amplifier = 0, particles = false)
    {
        this.sendDataPacket("mob_effect",  {
            runtime_entity_id: this.getId(),
            event_id: type,
            effect_id: effectId,
            duration: duration,
            amplifier: amplifier,
            particles: particles,
        })
    }

    sendGamemode(gamemode)
    {
        this.sendDataPacket('set_player_game_type', {
            gamemode: gamemode
        });
    }

    sendMotion(vector)
    {
        this.sendDataPacket('set_entity_motion', {
            runtime_entity_id: this.getId(),
            velocity: vector.asObject()
        });
    }

    sendDataPacket(name, data)
    {
        if(!this.isConnected) return;
        this.moduleManager.getAll().forEach((value) => {
            if(value.isEnabled()) {
                data = value.syncOutboundPacket(name, data);
            }
        });
        this.getBedrockPlayer().queue(name, data);
    }

    sendUpstreamDataPacket(name, data)
    {
        if(!this.isConnected) return;
        this.getBedrockPlayer().upstream.queue(name, data);
    }

    move(position, yaw = this.yaw, pitch = this.pitch, mode = "teleport")
    {
        this.sendDataPacket("move_player", {
            runtime_id: Number(this.getId()),
            position: position.asObject(),
            pitch: pitch,
            yaw: yaw,
            head_yaw: yaw,
            mode: mode,
            on_ground: true,
            ridden_runtime_id: 0,
            teleport: "unknown",
            tick: 32767
        });
    }

    getPositionForMoveToPosition(position, speed = 1) {

        let x = position.getX() - this.getPosition().getX();
        let y = position.getY() - this.getPosition().getY();
        let z = position.getZ() - this.getPosition().getZ();

        let diff = Math.abs(x) + Math.abs(z);
        if (x ** 2 + z ** 2 < 0.7) {
            this.motionVector.x = 0;
            this.motionVector.z = 0;
        } else if (diff > 0) {
            this.motionVector.x = 2.98 * speed * (x / diff);
            this.motionVector.z = 2.98 * speed * (z / diff);
        }

        return new Vector3(
            this.getPosition().getX(),
            position.getY(),
            this.getPosition().getZ()
        ).addVector(new Vector3(this.motionVector.x, this.motionVector.y, this.motionVector.z));
    }

    onUpdate()
    {
        if(!this.isConnected) return;
        this.moduleManager.tick();
    }

    sendSelfOffHand(items)
    {
        this.sendDataPacket("inventory_content", {
            window_id: "offhand",
            input: items
        });
    }

    sendOffHand(runtimeId, item)
    {
        this.sendDataPacket("mob_equipment", {
            runtime_entity_id: runtimeId,
            item: item,
            slot: 1,
            selected_slot: 0,
            window_id: "offhand"
        });
    }

    clearOffHand()
    {
        this.playersEntities.forEach((value) => {
            this.sendOffHand(value.getRuntimeId(), BasicItem.AIR.asObject());
        });
    }

    getRayTraceResult(flags, value)
    {
        let moveVec = new Vector3(0, 0, 0);
        let sessionPos = this.getPosition();
        let directionVector = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw, pitch: this.pitch}).getDirectionVector();
        if(flags.includes("w")) {
            moveVec.x = directionVector.x * value;
            moveVec.z = directionVector.z * value;
        }
        if(flags.includes("s")) {
            moveVec.x = -(directionVector.x * value);
            moveVec.z = -(directionVector.z * value);
        }
        if(flags.includes("q")) {
            if (flags.includes("w")) {
                let directionVectorRight = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw - 45, pitch: this.pitch}).getDirectionVector();
                moveVec.x += (directionVectorRight.x * value);
                moveVec.z += (directionVectorRight.z * value);
            } else if(flags.includes("s")) {
                let directionVectorRight = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw - 45, pitch: this.pitch}).getDirectionVector();
                moveVec.x -= (directionVectorRight.x * value);
                moveVec.z -= (directionVectorRight.z * value);
            } else {
                let directionVectorRight = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw - 90, pitch: this.pitch}).getDirectionVector();
                moveVec.x = (directionVectorRight.x * value);
                moveVec.z = (directionVectorRight.z * value);
            }
        }
        if(flags.includes("d")) {
            if (flags.includes("w")) {
                let directionVectorLeft = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw + 45, pitch: this.pitch}).getDirectionVector();
                moveVec.x += (directionVectorLeft.x * value);
                moveVec.z += (directionVectorLeft.z * value);
            } else if (flags.includes("s")) {
                let directionVectorLeft = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw + 45, pitch: this.pitch}).getDirectionVector();
                moveVec.x -= (directionVectorLeft.x * value);
                moveVec.z -= (directionVectorLeft.z * value);
            } else {
                let directionVectorLeft = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw + 90, pitch: this.pitch}).getDirectionVector();
                moveVec.x = (directionVectorLeft.x * value);
                moveVec.z = (directionVectorLeft.z * value);
            }
        }

        return moveVec;
    }

    transfer(address, port)
    {
        this.sendDataPacket('transfer', {
            server_address: `${address}`,
            port: parseInt(`${port}`)
        });
    }

    chat(message)
    {
        this.sendUpstreamDataPacket('text', {
            type: "chat",
            needs_translation: false,
            source_name: this.getName(),
            xuid: this.getXuid(),
            platform_chat_id: '',
            message: message
        });
    }

    sendMessage(message)
    {
        this.message("raw", message);
    }

    sendTip(message)
    {
        this.message("tip", message);
    }

    sendPopup(message)
    {
        this.message("popup", message);
    }

    message(type, message)
    {
        this.sendDataPacket('text', {
            type: type,
            needs_translation: false,
            source_name: "",
            xuid: "",
            platform_chat_id: '',
            message: message
        });
    }

    syncEntityData()
    {
        let hitboxwidth = this.hitbox.status ? this.hitbox.hitboxwidth : 0.6;
        let hitboxheight = this.hitbox.status ? this.hitbox.hitboxheight : 1.8;
        this.playersEntities.forEach((entity) => {
           this.sendDataPacket('set_entity_data', {
               runtime_entity_id: entity.getRuntimeId(),
               metadata: [
                   {
                       key: 'boundingbox_width',
                       type: 'float',
                       value: hitboxwidth
                   },
                   {
                       key: 'boundingbox_height',
                       type: 'float',
                       value: hitboxheight
                   }],
               properties: { ints: [], floats: [] },
               tick: 0n
           }) ;
        });
    }

    syncSelfData()
    {
    }

    syncTimer()
    {

    }

    syncTime()
    {
        let time = this.timechanger.status ? this.timechanger.time : 32767;
        if(time === 32767) return;
        this.sendDataPacket('set_time', {
            time: time
        });
    }

    syncAbilities()
    {
        let flying = this.noclip.status ? this.noclip.status : this.fly.status;
        let mayFly = this.fly.status;
        let noclip = this.noclip.status;
        let walkSpeed = 0.13999999523162842;
        let flySpeed = this.fly.status ? this.fly.speed : 0.05;
        this.sendDataPacket('update_abilities', {
            entity_unique_id: this.getId(),
            abilities: [{
                type: "base",
                permission_level: 'member',
                command_permission: 'normal',
                allowed: {
                    _value: 524287,
                    build: true,
                    mine: true,
                    doors_and_switches: true,
                    open_containers: true,
                    attack_players: true,
                    attack_mobs: true,
                    operator_commands: true,
                    teleport: true,
                    invulnerable: true,
                    flying: true,
                    may_fly: true,
                    instant_build: true,
                    lightning: true,
                    fly_speed: true,
                    walk_speed: true,
                    muted: true,
                    world_builder: true,
                    no_clip: true,
                    privileged_builder: true,
                    count: false
                },
                enabled: {
                    _value: 63,
                    build: true,
                    mine: true,
                    open_containers: true,
                    attack_mobs: true,
                    operator_commands: false,
                    teleport: false,
                    invulnerable: false,
                    flying: flying,
                    may_fly: mayFly,
                    instant_build: false,
                    lightning: false,
                    fly_speed: false,
                    walk_speed: false,
                    muted: false,
                    world_builder: false,
                    no_clip: noclip,
                    privileged_builder: false,
                    count: false
                },
                fly_speed: flySpeed,
                walk_speed: walkSpeed,
            }]
        });
    }

    canAttackOverEntity()
    {
        let distanceMax = 3;
        let sessionPos = this.getPosition();
        let directionVector = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.yaw, pitch: this.pitch}).getDirectionVector();
        this.playersEntities.forEach((entity) => {
            /*** @type PlayerEntity */
            let playerEntity =  entity;
            let position = entity.getPosition();

            for(let i = 0.0; i <= distanceMax; i++){
                let x = directionVector.x * i + sessionPos.getX();
                let y = directionVector.y * i + sessionPos.getY();
                let z = directionVector.z * i + sessionPos.getZ();

                let diffY = Math.abs(Math.round(y) - Math.round(position.getY()));
                if(
                    (Math.round(x) === Math.round(position.getX())) &&
                    (Math.round(y) === Math.round(position.getY()) && diffY <= 1.6) &&
                    (Math.round(z) === Math.round(position.getZ()))
                ) {
                    this.attack(playerEntity);
                    return playerEntity;
                }
            }
        });
        return null;
    }

    cleanWrite(data, defaultValue)
    {
        return data !== undefined && data !== null ? data : defaultValue;
    }

    sendCameraPresets()
    {
        this.sendDataPacket('camera_presets', {
            data: {
                type: "compound",
                name: "",
                value: {
                    presets: {
                        type: "list", value: {type: "compound", value: Presets.getPNXPresets()}
                    }
                }
            }
        });
    }
    
    sendCameraInstruction(cameraData)
    {
        this.sendDataPacket('camera_instruction', {
            data: cameraData.toObject()
        });
    }
}
module.exports = Session;