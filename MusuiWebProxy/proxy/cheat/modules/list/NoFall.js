const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class NoFall extends Module
{
    constructor(session) {
        super(session, "NoFall", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        if(packet.params.delta.y < -0.3) {
            packet.params.delta = new Vector3(0, 0, 0).asObject();
        }
        this.session.sendUpstreamDataPacket("move_player", {
            runtime_id: Number(this.session.getId()),
            position: packet.params.position,
            pitch: packet.params.pitch,
            yaw: packet.params.yaw,
            head_yaw: packet.params.yaw,
            mode: "normal",
            on_ground: true,
            ridden_runtime_id: 0,
            teleport: "unknown",
            tick: 32767
        });
    }
}
module.exports = NoFall;