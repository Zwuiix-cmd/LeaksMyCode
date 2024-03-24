class CameraPos
{
    data = {
        type: "compound",
        value: {
            pos: {
                type: "list",
                value: {
                    type: "float",
                    value: [
                        0, // X
                        0, // Y,
                        0, // Z
                    ]
                }
            }
        }
    };

    constructor(position)
    {
        this.data.value.pos.value.value[0] = position.getX();
        this.data.value.pos.value.value[1] = position.getY();
        this.data.value.pos.value.value[2] = position.getZ();
    }

    getName()
    {
        return "pos";
    }

    toObject()
    {
        return this.data;
    }
}
module.exports = CameraPos;