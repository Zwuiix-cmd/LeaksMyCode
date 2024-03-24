const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class NoRender extends Module
{
    constructor(session) {
        super(session, "NoRender", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "level_event") {
            return;
        }

        packet.data.canceled = true;
    }

}
module.exports = NoRender;