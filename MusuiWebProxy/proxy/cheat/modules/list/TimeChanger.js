const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class TimeChanger extends Module
{
    constructor(session) {
        super(session, "TimeChanger", {time: 0});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "set_time") {
            return;
        }

        packet.params.time = this.flags.time;
    }
}
module.exports = TimeChanger;