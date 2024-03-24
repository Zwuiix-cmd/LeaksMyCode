class Block
{
    runtimeId = 0;
    flags = {};
    layer = 0;

    constructor(
        runtimeId,
        flags,
        layer
    ) {
        this.runtimeId = runtimeId;
        this.flags = flags;
        this.layer = layer;
    }

    getId()
    {
        return this.runtimeId;
    }

    getFlags()
    {
        return this.flags;
    }

    getLayer()
    {
        return this.layer;
    }
}
module.exports = Block;