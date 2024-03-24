const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class AirJump extends Module
{
    constructor(session) {
        super(session, "AirJump", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        if(!this.session.isJumping) {
            return;
        }

        let moveVec = new Vector3(0, 0.42, 0);
        if(this.session.inputFlags.length !== 0) {
            let rayTraceResult = this.session.getRayTraceResult(this.session.inputFlags, 0.42);
            moveVec.x = rayTraceResult.x;
            moveVec.z = rayTraceResult.z;
        }

        this.session.sendMotion(moveVec);
    }
}
module.exports = AirJump;