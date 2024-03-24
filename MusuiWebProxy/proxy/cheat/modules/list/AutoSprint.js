const Module = require('../Module');
class AutoSprint extends Module
{
    constructor(session) {
        super(session, "AutoSprint", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        packet.params.input_data.sprinting = true;
        packet.params.input_data.start_sprinting = true;
        packet.params.input_data.stop_sprinting = false;
    }
}
module.exports = AutoSprint;