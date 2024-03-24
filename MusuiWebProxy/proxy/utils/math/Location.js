const {Vector3} = require("./Vector3");
const deg2rad = require('deg2rad');

class Location extends Vector3
{
    yaw = 0;
    pitch = 0;

    constructor(x, y, z, yaw, pitch)
    {
        super(x, y, z);
        this.yaw = yaw;
        this.pitch = pitch;
    }

    getYaw()
    {
        return this.yaw;
    }

    getPitch()
    {
        return this.pitch;
    }

    getDirectionVector()
    {
        let y = -Math.sin(deg2rad(this.getPitch()));
        let xz = Math.cos(deg2rad(this.getPitch()));
        let x = -xz * Math.sin(deg2rad(this.getYaw()));
        let z = xz * Math.cos(deg2rad(this.getYaw()));

        return this.normalize(new Vector3(x, y, z));
    }
}
module.exports = Location;