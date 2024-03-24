class CameraEase
{
    data = {
        type: "compound",
        value: {
            time: {
                type: "float",
                value: 0
            },
            type: {
                type: "string",
                value: "linear"
            }
        }
    };

    constructor(time, type = "linear")
    {
        this.data.value.time.value = time;
        this.data.value.type.value = type;
    }

    getName()
    {
        return "ease";
    }

    toObject()
    {
        return this.data;
    }
}
module.exports = CameraEase;