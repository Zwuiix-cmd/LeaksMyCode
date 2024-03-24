const Vector2 = require("./Vector2");
module.exports = {
    calculateAgreedDirection(pos, newPos)
    {
        let xdiff = pos.x - newPos.x;
        let zdiff = pos.z - newPos.z;
        let angle = Math.atan2(zdiff, xdiff);
        let yaw = ((angle * 180) / 3.1415926535898) - 90;
        let ydiff = pos.y - newPos.y;
        let v = new Vector2(newPos.x, newPos.z);
        let dist = v.distance(new Vector2(pos.x, pos.z));
        angle = Math.atan2(dist, ydiff);
        let pitch = ((angle * 180) / 3.1415926535898) - 90;

        return new Vector2(yaw, pitch);
    }
}