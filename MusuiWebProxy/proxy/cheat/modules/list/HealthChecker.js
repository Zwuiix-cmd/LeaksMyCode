const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
const EasyProxyPosition = require("../../../utils/EasyProxyPosition");
const PlayerEntity = require("../../entities/PlayerEntity");
class HealthChecker extends Module
{
    constructor(session) {
        super(session, "HealthChecker", {});
    }

    handlePacket(type, packet)
    {
        if(type === "clientbound" && packet.name === "update_attributes") {
            if(packet.params.runtime_entity_id != this.session.getId()) {
                if (this.session.cursorEntity !== null) {
                    let entity = this.session.cursorEntity;
                    if(entity instanceof PlayerEntity) {
                        packet.params.attributes.forEach((attribute) => {
                            let pourcent = attribute.current / session.healthchecker.pourcent;
                            if(attribute.name === "minecraft:health") {
                                this.session.sendPopup(pourcent + "/" + this.session.healthchecker.pourcent);
                            }
                        });
                    }
                }
            }
        }
    }
}
module.exports = HealthChecker;