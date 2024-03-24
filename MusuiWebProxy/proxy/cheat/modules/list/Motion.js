const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Motion extends Module
{
    constructor(session) {
        super(session, "Motion", {speed: 1});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.motion_speed_value, 1);
    }

    onTick()
    {
        if(this.session.inputFlags.length === 0) {
            this.session.sendMotion(new Vector3(0, 0.002, 0));
            this.session.sendMotion(new Vector3(0, -0.001, 0));
        } else this.session.sendMotion(this.session.getRayTraceResult(this.session.inputFlags, this.flags.speed));
    }
}
module.exports = Motion;