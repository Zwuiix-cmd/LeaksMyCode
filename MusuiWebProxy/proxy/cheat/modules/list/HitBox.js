const Module = require('../Module');
class HitBox extends Module
{
    constructor(session) {
        super(session, "HitBox", {width: 0.6, height: 1.8});
    }

    onUpdate(data)
    {
        this.flags.width = this.cleanValue(data.hitboxwidth, 0.6);
        this.flags.height = this.cleanValue(data.hitboxheight, 1.8);
    }

    syncOutboundPacket(name, packetData)
    {
        if(name === "set_entity_data") {
            packetData.metadata.forEach((data) => {
                if(data.key === "boundingbox_width") {
                    data.value = this.flags.width;
                }
                if(data.key === "boundingbox_height") {
                    data.value = this.flags.height;
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
            packet.params.metadata.forEach((data) => {
                if(data.key === "boundingbox_width") {
                    data.value = this.flags.width;
                }
                if(data.key === "boundingbox_height") {
                    data.value = this.flags.height;
                }
            });
        }
    }
}
module.exports = HitBox;