const Module = require('../Module');
class Velocity extends Module
{
    constructor(session) {
        super(session, "Velocity", {horizontal: 98, vertical: 98});
    }

    onUpdate(data)
    {
        this.flags.horizontal = this.cleanValue(data.kbhorizontal, 98);
        this.flags.vertical = this.cleanValue(data.kbvertical, 98);
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "set_entity_motion") {
            return;
        }

        let x = packet.params.velocity.x;
        let y = packet.params.velocity.y;
        let z = packet.params.velocity.z;
        packet.params.velocity = {x: x / (100 / this.flags.horizontal), y: y / (100 / this.flags.vertical), z: z / (100 / this.flags.horizontal)};
    }
}
module.exports = Velocity;