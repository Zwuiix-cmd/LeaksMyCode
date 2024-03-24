const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class NoSound extends Module
{
    constructor(session) {
        super(session, "NoSound", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "level_sound_event") {
            return;
        }

        packet.params.sound_id = "";
        packet.params.position = new Vector3(0, 0, 0).asObject();
    }

}
module.exports = NoSound;