const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Fly extends Module
{
    constructor(session) {
        super(session, "Fly", {speed: 1});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.fly_speed_value, 1);
    }

    syncOutboundPacket(name, packetData)
    {
        if(name === "update_abilities") {
            let abilities = packetData.abilities[0];
            abilities.enabled.flying = true;
            abilities.enabled.mayFly = true;
            abilities.fly_speed = (0.05 * this.flags.speed);
        }

        return packetData;
    }

    handlePacket(type, packet)
    {
        if(type === "serverbound" && packet.name === "request_ability") {
            if(packet.params.ability === "flying") {
                packet.params.bool_value = false;
            }
        }
        if(type === "clientbound" && packet.name === "update_abilities") {
            if(packet.params.entity_unique_id === this.session.getId()) {
                let value = packet.params.abilities[0];
                if(value.type === "base") {
                    value.fly_speed = (0.05 * this.flags.speed);
                    value.enabled.flying = true;
                    value.enabled.may_fly = true;
                }
            }
        }
    }
}
module.exports = Fly;