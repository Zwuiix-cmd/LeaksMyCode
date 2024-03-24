const Module = require('../Module');
class AntiBlind extends Module
{
    constructor(session) {
        super(session, "AntiBlind", {});
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

        switch (packet.name) {
            case "mob_effect":
                if(packet.params.event_id !== 1) {
                    return;
                }

                if(packet.params.effect_id === "") {

                }
                break;
            case "set_entity_data":
                if(packet.params.runtime_entity_id !== this.session.getId()) {
                    packet.params.metadata.forEach((value) => {
                        if(value.key === "flags" && value.type === "long" && value.value !== undefined) {
                            value.value.on_fire = false;
                            value.value.invisible = false;
                        }
                    });
                }
                break;
        }
    }
}
module.exports = AntiBlind;