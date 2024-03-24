class CameraRot
{
    data = {
        type: "compound",
        value: {
            x: {
                type: "float",
                value: 0 // YAW
            },
            y: {
                type: "float",
                value: 0 // PITCH
            }
        }
    };

    constructor(vector2)
    {
        this.data.value.x.value = vector2.getX();
        this.data.value.y.value = vector2.getZ();
    }

    getName()
    {
        return "rot";
    }

    toObject()
    {
        return this.data;
    }
}
module.exports = CameraRot;