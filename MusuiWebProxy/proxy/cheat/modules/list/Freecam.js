const Module = require('../Module');
const MathJS = require("../../../utils/MathJS");
const {Vector3} = require("../../../utils/math/Vector3");
const Vector2 = require("../../../utils/math/Vector2");
class Freecam extends Module
{
    constructor(session) {
        super(session, "Freecam", {});
    }

    onUpdate(data)
    {
        if(this.status) {
            if(this.session.lastPositionFreecam.asObject() === new Vector3(0, 0, 0).asObject()) {
                this.session.lastPositionFreecam = this.position;
            }
            if(this.session.lastRotationsFreecam.asObject() === new Vector2(0, 0).asObject()) {
                this.session.lastRotationsFreecam = new Vector2(this.yaw, this.pitch);
            }
            this.session.sendGamemode(6);
        } else {
            this.session.lastPositionFreecam = new Vector3(0, 0, 0);
            this.session.lastRotationsFreecam = new Vector2(0, 0);
            this.session.sendGamemode(0);
        }
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input"){
            return;
        }

        packet.params.position = this.session.lastPositionFreecam.asObject();
        packet.params.yaw = this.session.lastRotationsFreecam.getX();
        packet.params.pitch = this.session.lastRotationsFreecam.getZ();
    }
}
module.exports = Freecam;