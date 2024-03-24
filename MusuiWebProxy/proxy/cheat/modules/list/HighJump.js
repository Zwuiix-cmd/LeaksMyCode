const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class HighJump extends Module
{
    constructor(session) {
        super(session, "HighJump", {speed: 1});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.highjump_jumpheight_value, 1);
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input"){
            return;
        }

        if(packet.params.input_data.start_jumping) {
            this.session.sendMotion(new Vector3(0, this.flags.speed, 0));
        }
    }
}
module.exports = HighJump;