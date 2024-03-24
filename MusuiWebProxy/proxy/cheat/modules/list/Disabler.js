const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Disabler extends Module
{
    constructor(session) {
        super(session, "Disabler", {});
    }

    onTick()
    {
        this.session.sendMotion(new Vector3(0, 0.0001, 0));
    }

    handlePacket(type, packet)
    {
        if(type === "clientbound") {
            if(packet.name === "move_player") {
                if(packet.params.runtime_id == this.session.getId()) {
                    packet.params.on_ground = true;
                }
            }
            if(packet.name === "player_auth_input") {
                packet.params.move_vector = new Vector3(0.01, 0.01).asObject();
            }
            if(packet.name === "level_sound_event") {
                if(packet.params.sound_id === "AttackStrong" || packet.params.sound_id === "AttackNoDamage") {
                    packet.params.sound_id = "Undefined";
                }
            }
        }
    }
}
module.exports = Disabler;