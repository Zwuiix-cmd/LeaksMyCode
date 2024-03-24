const Module = require('../Module');
class PacketLogger extends Module
{
    constructor(session) {
        super(session, "PacketLogger", {});
    }

    handlePacket(type, packet)
    {
        if(packet.name === "text") return;
        this.session.sendMessage(`§aPacketLogger §2> §rPacket:${packet.name} Type: ${type}`);
    }

}
module.exports = PacketLogger;