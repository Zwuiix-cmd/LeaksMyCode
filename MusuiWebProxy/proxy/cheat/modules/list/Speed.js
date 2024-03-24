const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
const EasyProxyPosition = require("../../../utils/EasyProxyPosition");
class Speed extends Module
{
    constructor(session) {
        super(session, "Speed", {value: 0.10});
    }

    onUpdate(data)
    {
        this.flags.value = this.cleanValue(data.speed_value, 0.10);
    }

    handlePacket(type, packet) {
        if(type === "clientbound" && packet.name === "update_attributes") {
            if(packet.params.runtime_entity_id == this.session.getId()) {
                packet.params.attributes.push({
                    min: 0,
                    max: 3.4028234663852886e+38,
                    current: (0.15 * this.flags.value),
                    default: (0.15 * this.flags.value),
                    name: 'minecraft:movement',
                    modifiers: []
                });
            }
        }
    }
}
module.exports = Speed;