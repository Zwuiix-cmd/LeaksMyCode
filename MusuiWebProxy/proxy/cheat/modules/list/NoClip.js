const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class NoClip extends Module
{
    constructor(session) {
        super(session, "NoClip", {});
    }

    syncOutboundPacket(name, packetData)
    {
        if(name === "update_abilities") {
            let abilities = packetData.abilities[0];
            abilities.enabled.flying = true;
            abilities.enabled.mayFly = true;
            abilities.enabled.no_clip = true;
        }

        return packetData;
    }

    handlePacket(type, packet)
    {
        if(type === "serverbound" && packet.name === "request_ability") {
            if(packet.params.ability === "flying") {
                packet.params.bool_value = false;
            }
        }
        if(type === "clientbound" && packet.name === "update_abilities") {
            if(packet.params.entity_unique_id == this.session.getId()) {
                let value = packet.params.abilities[0];
                if(value.type === "base") {
                    value.enabled.flying = true;
                    value.enabled.no_clip = true;
                    value.enabled.may_fly = true;
                }
            }
        }
    }
}
module.exports = NoClip;