const Module = require('../Module');
class AntiImmobile extends Module
{
    constructor(session) {
        super(session, "AntiImmobile", {});
    }

    syncOutboundPacket(name, packetData)
    {
        if(name === "set_entity_data") {
            packetData.metadata.forEach((data) => {
                if(data.key === "flags" && data.type === "long" && data.value !== undefined) {
                    data.value.no_ai = false;
                }
            });
        }
        return packetData;
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "set_entity_data") {
            return;
        }

        if(packet.params.runtime_entity_id !== this.session.getId()) {
            packet.params.metadata.forEach((value) => {
                if(value.key === "flags" && value.type === "long" && value.value !== undefined) {
                    value.value.no_ai = false;
                }
            });
        }
    }
}
module.exports = AntiImmobile;