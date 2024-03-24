const AutoClicker = require("./list/AutoClicker");
const {contentDisposition} = require("express/lib/utils");
const EffectMap = require("../effect/EffectMap");
const EffectIds = require("../effect/EffectIds");
const AirJump = require("./list/AirJump");
const AntiImmobile = require("./list/AntiImmobile");
const AntiVoid = require("./list/AntiVoid");
const AutoSneak = require("./list/AutoSneak");
const AutoSprint = require("./list/AutoSprint");
const Bhop = require("./list/Bhop");
const BigBitch = require("./list/BigBitch");
const ClickTP = require("./list/ClickTP");
const DeathPosition = require("./list/DeathPosition");
const Derp = require("./list/Derp");
const Disabler = require("./list/Disabler");
const FastEat = require("./list/FastEat");
const Fly = require("./list/Fly");
const Franky = require("./list/Franky");
const Freecam = require("./list/Freecam");
const Fullbright = require("./list/Fullbright");
const Glide = require("./list/Glide");
const HealthChecker = require("./list/HealthChecker");
const HighJump = require("./list/HighJump");
const HitBox = require("./list/HitBox");
const InstantBreak = require("./list/InstantBreak");
const JetPack = require("./list/JetPack");
const KillAura = require("./list/KillAura");
const Motion = require("./list/Motion");
const NoClip = require("./list/NoClip");
const NoFall = require("./list/NoFall");
const NoHurtCam = require("./list/NoHurtCam");
const NoRender = require("./list/NoRender");
const NoSound = require("./list/NoSound");
const PacketLogger = require("./list/PacketLogger");
const PacketLoss = require("./list/PacketLoss");
const Reach = require("./list/Reach");
const Spammer = require("./list/Spammer");
const Speed = require("./list/Speed");
const TimeChanger = require("./list/TimeChanger");
const Velocity = require("./list/Velocity");
const Timer = require("./list/Timer");
const Nuker = require("./list/Nuker");
const AutoFarm = require("./list/AutoFarm");

class ModuleManager
{
    session;

    modules = new Map();

    constructor(session)
    {
        this.session = session;
        this.register(new AirJump(session));
        this.register(new AntiImmobile(session));
        this.register(new AntiVoid(session));
        this.register(new AutoClicker(session));
        this.register(new AutoFarm(session));
        this.register(new AutoSneak(session));
        this.register(new AutoSprint(session));
        this.register(new Bhop(session));
        this.register(new BigBitch(session));
        this.register(new ClickTP(session));
        this.register(new DeathPosition(session));
        this.register(new Derp(session));
        this.register(new Disabler(session));
        this.register(new FastEat(session));
        this.register(new Fly(session));
        this.register(new Franky(session));
        this.register(new Freecam(session));
        this.register(new Fullbright(session));
        this.register(new Glide(session));
        this.register(new HealthChecker(session));
        this.register(new HighJump(session));
        this.register(new HitBox(session));
        this.register(new InstantBreak(session));
        this.register(new JetPack(session));
        this.register(new KillAura(session));
        this.register(new Motion(session));
        this.register(new NoClip(session));
        this.register(new NoFall(session));
        this.register(new NoHurtCam(session));
        this.register(new NoRender(session));
        this.register(new NoSound(session));
        this.register(new Nuker(session));
        this.register(new PacketLogger(session));
        this.register(new PacketLoss(session));
        this.register(new Reach(session));
        this.register(new Spammer(session));
        this.register(new Speed(session));
        this.register(new TimeChanger(session));
        this.register(new Timer(session));
        this.register(new Velocity(session));
    }

    register(module)
    {
        if(this.modules.has(module.getName())) {
            throw new Error(`Module ${module.getName()} already registered!`);
        }

        this.modules.set(module.getName(), module);
    }

    getAll()
    {
        return this.modules;
    }

    handlePacket(type, packet)
    {
        this.getAll().forEach((value) => {
            if(value.isEnabled()) {
                value.handlePacket(type, packet);
            }
        });
    }

    syncModule(data)
    {
        if(data.self_destruct !== undefined) {
            let proxy = this.session.getProxy();
            this.session.transfer(proxy.getDestHost(), proxy.getDestPort());
            return;
        }

        if(data.disable_all !== undefined) {
            this.getAll().forEach((value) => {
                value.setEnabled(false);
                value.onUpdate({});
                this.syncData();
            });
            return;
        }

        this.getAll().forEach((value) => {
            value.setEnabled(data[`${value.getName().toLowerCase()}_status`] === "on");
            value.onUpdate(data);
        });

        this.syncData();
    }

    syncData()
    {
        this.session.sendEffect(EffectMap.REMOVE, EffectIds.NIGHT_VISION);
        this.session.sendDataPacket('set_entity_data', {
            runtime_entity_id: this.session.getId(),
            metadata: [
                {
                    key: 'flags',
                    type: 'long',
                    value: {
                        _value: 844459290411008n,
                        onfire: false,
                        sneaking: false,
                        riding: false,
                        sprinting: false,
                        action: false,
                        invisible: false,
                        tempted: false,
                        inlove: false,
                        saddled: false,
                        powered: false,
                        ignited: false,
                        baby: false,
                        converting: false,
                        critical: false,
                        can_show_nametag: true,
                        always_show_nametag: false,
                        no_ai: false,
                        silent: false,
                        wallclimbing: false,
                        can_climb: true,
                        swimmer: false,
                        can_fly: false,
                        walker: false,
                        resting: false,
                        sitting: false,
                        angry: false,
                        interested: false,
                        charged: false,
                        tamed: false,
                        orphaned: false,
                        leashed: false,
                        sheared: false,
                        gliding: false,
                        elder: false,
                        moving: false,
                        breathing: true,
                        chested: false,
                        stackable: false,
                        showbase: false,
                        rearing: false,
                        vibrating: false,
                        idling: false,
                        evoker_spell: false,
                        charge_attack: false,
                        wasd_controlled: false,
                        can_power_jump: false,
                        can_dash: false,
                        linger: false,
                        has_collision: true,
                        affected_by_gravity: true,
                        fire_immune: false,
                        dancing: false,
                        enchanted: false,
                        show_trident_rope: false,
                        container_private: false,
                        transforming: false,
                        spin_attack: false,
                        swimming: false,
                        bribed: false,
                        pregnant: false,
                        laying_egg: false,
                        rider_can_pick: false,
                        transition_sitting: false,
                        eating: false,
                        laying_down: false
                    }
                },
            ],
            properties: { ints: [], floats: [] },
            tick: 0n
        });

        this.session.playersEntities.forEach((entity) => {
            this.session.sendDataPacket('set_entity_data', {
                runtime_entity_id: entity.getRuntimeId(),
                metadata: [
                    {
                        key: 'boundingbox_width',
                        type: 'float',
                        value: 0.6
                    },
                    {
                        key: 'boundingbox_height',
                        type: 'float',
                        value: 1.8
                    }],
                properties: { ints: [], floats: [] },
                tick: 0n
            }) ;
        });

        this.session.sendDataPacket('update_abilities', {
            entity_unique_id: this.session.getId(),
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
                    flying: false,
                    may_fly: false,
                    instant_build: false,
                    lightning: false,
                    fly_speed: false,
                    walk_speed: false,
                    muted: false,
                    world_builder: false,
                    no_clip: false,
                    privileged_builder: false,
                    count: false
                },
                fly_speed: 0.05,
                walk_speed: 0.15,
            }]
        });

        this.session.sendDataPacket("level_event", {
            event: "set_game_speed",
            position: {x: 1, y: 0, z: 0},
            data: 0
        });
    }

    tick()
    {
        this.getAll().forEach((value) => {
            if(value.isEnabled()) {
                value.onTick();
            }
        });
    }
}
module.exports = ModuleManager;