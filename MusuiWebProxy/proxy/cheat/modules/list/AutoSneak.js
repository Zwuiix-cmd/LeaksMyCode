const Module = require('../Module');
class AutoSneak extends Module
{
    constructor(session) {
        super(session, "AutoSneak", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        packet.params.input_data.sneaking = true;
        packet.params.input_data.start_sneaking = true;
        packet.params.input_data.stop_sneaking = true;
    }
}
module.exports = AutoSneak;