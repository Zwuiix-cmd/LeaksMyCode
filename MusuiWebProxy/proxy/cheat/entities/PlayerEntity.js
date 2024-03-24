const {Vector3} = require("../../utils/math/Vector3");
const Vector2 = require("../../utils/math/Vector2");

class PlayerEntity
{
    name = "";
    uuid = "";
    deviceId = "";
    deviceOs = "";
    runtimeId = "0";
    position = new Vector3(0,0,0);
    rotation = new Vector2(0, 0);

    constructor(
        name,
        uuid,
        runtimeId,
        position,
        rotation,
        deviceId,
        deviceOs,
    ) {
        this.name = name;
        this.uuid = uuid;
        this.runtimeId = runtimeId;
        this.position = position;
        this.rotation = rotation;
        this.deviceId = deviceId;
        this.deviceOs = deviceOs;
    }

    getName()
    {
        return this.name;
    }

    getUUID()
    {
        return this.uuid;
    }

    getRuntimeId()
    {
        return this.runtimeId;
    }

    getPosition()
    {
        return this.position;
    }

    setPosition(position)
    {
        this.position = position;
    }

    getRotation()
    {
        return this.rotation;
    }

    getDeviceId()
    {
        return this.deviceId;
    }

    getDeviceOs()
    {
        return this.deviceOs;
    }
}
module.exports = PlayerEntity;