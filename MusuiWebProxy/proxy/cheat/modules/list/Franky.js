const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Franky extends Module
{
    constructor(session) {
        super(session, "Franky", {});
    }

    handlePacket(type, packet)
    {
        if(type === "clientbound") {
            if (packet.name === "player_auth_input") {
                if (this.session.frankyCountPlayerAuthInput >= 10) {
                    this.session.frankyCountPlayerAuthInput = 0;
                    packet.params.position = new Vector3(0, 0, 0).asObject();
                } else this.session.frankyCountPlayerAuthInput++;

            }
        }
    }
}
module.exports = Franky;