const Module = require('../Module');
const MathJS = require("../../../utils/MathJS");
class Derp extends Module
{
    constructor(session) {
        super(session, "Derp", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        packet.params.yaw = MathJS.rand(-180, 180);
        packet.params.pitch = MathJS.rand(-180, 180);
    }
}
module.exports = Derp;