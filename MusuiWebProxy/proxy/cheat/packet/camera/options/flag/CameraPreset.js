class CameraPreset
{
    data = {
        type: "int",
        value: 1
    };

    constructor(type = this.TAG_FIRST_PERSON)
    {
        this.data.value = type;
    }

    getName()
    {
        return "preset";
    }

    toObject()
    {
        return this.data;
    }
}
module.exports = CameraPreset;