const Module = require('../Module');
const Others = require("../../../utils/math/Others");
const EasyProxyPosition = require("../../../utils/EasyProxyPosition");
const {Vector3} = require("../../../utils/math/Vector3");
class KillAura extends Module
{
    target = null;

    constructor(session) {
        super(session, "KillAura", {reach: 3, clicker: 10, single: false});
    }

    onUpdate(data)
    {
        this.flags.reach = this.cleanValue(data.killaura_range, 3);
        this.flags.clicker = this.cleanValue(data.killaura_clicker_value, 10);
        this.flags.single = data.killaura_single === "on";
    }

    onTick()
    {
        let maxDistance = this.flags.reach;
        if(this.flags.single) {
            let lastTargetDistance = 30;
            this.session.playersEntities.forEach((entity) => {
                let distance = entity.getPosition().distance(this.session.getPosition());
                if(distance <= lastTargetDistance) {
                    this.target = entity;
                }
            });
            if(this.target === null) {
                return;
            }

            if(this.target.getPosition().distance(this.session.getPosition()) <= maxDistance) {
                this.session.attack(this.target);
            }

            if(this.session.inputFlags.includes("w")) {
                let position = this.session.getPositionForMoveToPosition(this.target.getPosition(), 0.75);

                let distance = this.target.getPosition().distance(this.session.getPosition());
                if(distance >= 4.5 && distance <= 10) {
                    let direction = Others.calculateAgreedDirection(this.target.getPosition(), this.session.getPosition());
                    let directionVector = new EasyProxyPosition({
                        x: this.session.getPosition().x,
                        y: this.session.getPosition().y,
                        z: this.session.getPosition().z,
                        yaw: direction.getX() + 90,
                        pitch: direction.getZ()
                    }).getDirectionVector();
                    position = position.addVector(new Vector3(directionVector.x, directionVector.y, directionVector.z));
                }

                if(distance >= 4.5) {
                    this.session.move(position, this.session.yaw, this.session.pitch, "normal");
                }
            }

            return;
        }

        this.session.playersEntities.forEach((entity) => {
            /*** @type PlayerEntity */
            let playerEntity =  entity;
            let position = entity.getPosition();

            if(position.distance(this.session.getPosition()) <= maxDistance) {
                this.session.attack(playerEntity);
            }
        });
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        if(!this.flags.single) {
            return;
        }

        if(this.target === null) {
            return;
        }

        if(this.target.getPosition().distance(this.session.getPosition()) > 30) {
            return;
        }

        let direction = Others.calculateAgreedDirection(this.target.getPosition(), this.session.getPosition());
        packet.params.yaw = direction.getX();
        packet.params.pitch = direction.getZ();
    }
}
module.exports = KillAura;