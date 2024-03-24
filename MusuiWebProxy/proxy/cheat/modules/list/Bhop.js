const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class Bhop extends Module
{
    constructor(session) {
        super(session, "Bhop", {speed: 0.10});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.motion_speed_value, 1);
    }

    onTick()
    {
        if(this.session.inputFlags.length !== 0) {
            if(this.session.onGround) {
                let speed = this.flags.speed;

                let moveVec = this.session.getRayTraceResult(this.session.inputFlags, speed);
                moveVec.y = 0.42;
                this.session.sendMotion(moveVec);
                this.session.onGround = false;
            }
        }
    }
}
module.exports = Bhop;