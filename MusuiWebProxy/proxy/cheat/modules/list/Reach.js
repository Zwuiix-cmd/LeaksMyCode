const Module = require('../Module');
class Reach extends Module
{
    constructor(session) {
        super(session, "Reach", {reach: 3, sprinting: false});
    }

    onUpdate(data)
    {
        this.flags.reach = data.reach_range === undefined ? 3 : data.reach_range;
        this.flags.sprinting = data.reach_sprinting === "on";
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "animate") {
            return;
        }

        let enable = true;
        if(this.flags.sprinting) {
            if(!this.session.isSprinting) enable = false;
        }
        if(enable) {
            let resp = this.session.canAttackOverEntity();
            if(resp !== null) {
                this.session.attack(resp);
            }
        }
    }
}
module.exports = Reach;