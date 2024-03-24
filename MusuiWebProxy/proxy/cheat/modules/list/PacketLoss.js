const Module = require('../Module');
class PacketLoss extends Module
{
    constructor(session) {
        super(session, "PacketLoss", {rate: 1.0});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(Math.random() > this.flags.rate) {
            return;
        }

        packet.data.canceled = true;
    }

}
module.exports = PacketLoss;