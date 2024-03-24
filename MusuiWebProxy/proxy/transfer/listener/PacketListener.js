const ModalFormResponsePacket = require("../packet/ModalFormResponsePacket");

class PacketListener
{
    constructor(client)
    {
        client.on('packet', (packet) => {
            console.log(packet)
        });
    }
}
module.exports = PacketListener;