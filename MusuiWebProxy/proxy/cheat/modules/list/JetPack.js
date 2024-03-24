const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
const EasyProxyPosition = require("../../../utils/EasyProxyPosition");
class JetPack extends Module
{
    constructor(session) {
        super(session, "JetPack", {speed: 1});
    }

    onUpdate(data)
    {
        this.flags.speed = this.cleanValue(data.jetpack_speed_value, 1);
    }

    onTick()
    {
        if(this.session.inputFlags.length !== 0) {
            if(this.session.inputFlags.includes("w")) {
                let speed = this.flags.speed;
                let moveVec = new Vector3(0, 0, 0);
                let sessionPos = this.session.getPosition();
                let directionVector = new EasyProxyPosition({x: sessionPos.x, y: sessionPos.y, z: sessionPos.y, yaw: this.session.yaw, pitch: this.session.pitch}).getDirectionVector();

                moveVec.x = directionVector.x * speed;
                moveVec.y = directionVector.y * speed;
                moveVec.z = directionVector.z * speed;

                this.session.sendMotion(moveVec);
            }
        }
    }
}
module.exports = JetPack;