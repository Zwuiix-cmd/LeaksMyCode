const Vector2 = require("../../utils/math/Vector2");
const Block = require("../block/Block");

class Chunk
{
    chunkPosition;
    blocks = new Map();

    constructor(x, z)
    {
        this.chunkPosition = new Vector2(x, z);
    }


    setBlock(position, block)
    {
        this.blocks.set(`${position.getFloorX()}:${position.getFloorY()}:${position.getFloorZ()}`, block);
    }

    getBlock(x, y, z)
    {
        return this.blocks.get(`${x}:${y}:${z}`) ?? new Block(0, {}, 0);
    }

    getBlocks()
    {
        return this.blocks;
    }
}
module.exports = Chunk;