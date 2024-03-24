const Module = require('../Module');
const {Vector3, fromObject} = require("../../../utils/math/Vector3");
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
const BlockkHardingIds = require("../../block/BlockHardingIds");
const ItemIds = require("../../item/ItemIds");
class AutoFarm extends Module
{
    targetsBlocks = new Map();
    target = undefined;
    randomTag = 0;

    constructor(session) {
        super(session, "AutoFarm", {});
    }

    onTick()
    {

        if(this.target !== undefined) {
            let sPos = this.session.getPosition();
            if(this.target.position.distance(sPos) <= 2) {
                this.session.breakBlock(this.target.position);
                this.target = undefined;
                return;
            }

            let calculateMove = this.session.getPositionForMoveToPosition(this.target.position.add(0, 1.62, 0), 0.75);
            let vector = Others.calculateAgreedDirection(this.target.position, this.session.getPosition());
            this.session.move(calculateMove, vector.getX(), vector.getZ(), "normal");

            return;
        }

        let lastTargetDistance = 150;
        let target= null;
        this.targetsBlocks.forEach((value, key, map) => {
            let distance = value.position.distance(this.session.getPosition());
            if(distance <= lastTargetDistance) {
                target = {value: value, key: key};
            }
        });
        if(target !== null) {
            this.target = target.value;
            this.targetsBlocks.delete(target.key);
        }
    }

    handlePacket(type, packet)
    {
        if(type === "serverbound") {
            if(packet.name === "player_auth_input") {
                if(this.target !== undefined) {
                    let vector = Others.calculateAgreedDirection(this.target.position, this.session.getPosition());
                    packet.params.yaw = vector.x;
                    packet.params.pitch = vector.z;
                }
            }
        }else if(type === "clientbound") {
            if(packet.name === "update_block") {
                let id = packet.params.block_runtime_id;
                let position = packet.params.position;
                if(id === BlockkHardingIds.MELON || id === BlockkHardingIds.PUMPKIN) {
                    this.targetsBlocks.set(this.randomTag, {
                        id: id,
                        position: fromObject(position)
                    });
                    this.randomTag++;
                }
            }
        }
    }
}
module.exports = AutoFarm;