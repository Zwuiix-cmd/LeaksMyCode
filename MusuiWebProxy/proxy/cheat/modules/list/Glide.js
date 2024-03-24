const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Glide extends Module
{
    constructor(session) {
        super(session, "Glide", {speed: -0.15});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.glide_speed_value, -0.15);
    }

    onTick()
    {
        this.session.sendMotion(new Vector3(0, 0.000002, 0));
        this.session.sendMotion(new Vector3(0, this.flags.speed, 0));
    }
}
module.exports = Glide;