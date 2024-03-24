const Module = require('../Module');
const MathJS = require("../../../utils/MathJS");
class NoHurtCam extends Module
{
    constructor(session) {
        super(session, "NoHurtCam", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "entity_event") {
            return;
        }

        if(packet.params.runtime_entity_id !== this.session.getId()) {
            return;
        }

        if(packet.params.event_id !== "hurt_animation") {
            return;
        }

        packet.data.canceled = true;
    }

}
module.exports = NoHurtCam;