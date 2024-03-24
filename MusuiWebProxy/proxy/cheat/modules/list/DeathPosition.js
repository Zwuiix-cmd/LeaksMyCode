const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class DeathPosition extends Module
{
    constructor(session) {
        super(session, "DeathPosition", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "clientbound") {
            return;
        }

        if(packet.name !== "death_info") {
            return;
        }

        this.session.sendMessage(`§aYour DeathPosition: §2${this.session.getPosition().asString()}`);
    }
}
module.exports = DeathPosition;