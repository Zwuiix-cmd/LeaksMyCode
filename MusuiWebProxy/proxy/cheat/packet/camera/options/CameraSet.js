class CameraSet
{
    data = {
        type: "compound",
        value: {}
    }

    constructor()
    {
    }

    add(flag)
    {
        this.data.value[flag.getName()] = flag.toObject();
    }

    toObject()
    {
        return {set: this.data};
    }
}
module.exports = CameraSet;