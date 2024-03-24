const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
class AntiVoid extends Module
{
    constructor(session) {
        super(session, "AntiVoid", {});
    }

    onTick()
    {
        if(this.session.getPosition().getY() <= -5) {
            this.session.sendMotion(new Vector3(0, 0.001, 0));
        }
    }
}
module.exports = AntiVoid;