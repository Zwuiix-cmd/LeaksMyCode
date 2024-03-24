const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Timer extends Module
{
    constructor(session) {
        super(session, "Timer", {value: 1});
    }

    onUpdate(data)
    {
        this.flags.value = this.cleanValue(data.timer_value, 1);
    }

    syncOutboundPacket(name, packetData)
    {
        if(name === "level_event") {
            if(packetData.event === "set_game_speed") {
                packetData.position = new Vector3(this.flags.value, 0, 0).asObject();
            }
        }
        return packetData;
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "level_event") {
            return;
        }

        if(packet.params.event === "set_game_speed") {
            packet.params.position = {x: this.flags.value, y: 0, z: 0};
        }
    }
}
module.exports = Timer;