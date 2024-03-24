const Chunk = require("./Chunk");
const {Vector3} = require("../../utils/math/Vector3");

class World
{
    chunks = new Map();

    constructor()
    {
    }

    setBlockAt(position, block)
    {
        let chunk = this.getChunk(position);
        if(chunk === undefined || chunk === null) {
            let x = position.getFloorX() >> 4;
            let z = position.getFloorZ() >> 4;
            chunk = new Chunk(x, z);
            this.chunks.set(`${x}:${z}`, chunk);
        }

        const localX = position.getFloorX() - (position.getFloorX() >> 4) * 16;
        const localY = position.getFloorY();
        const localZ = position.getFloorZ() - (position.getFloorZ() >> 4) * 16;

        chunk.setBlock(new Vector3(localX, localY, localZ), block);
    }

    getBlockAt(position)
    {
        const localX = position.getFloorX() - (position.getFloorX() >> 4) * 16;
        const localY = position.getFloorY();
        const localZ = position.getFloorZ() - (position.getFloorZ() >> 4) * 16;

        return this.getChunk(position).getBlock(localX, localY, localZ);
    }

    pushChunk(x, z)
    {
        if(this.getChunk(new Vector3(x, 0, z)) !== undefined) {
            return;
        }
        this.chunks.set(`${x}:${z}`, new Chunk(x, z));
    }

    /**
     *
     * @returns {Chunk|null}
     * @param position
     */
    getChunk(position)
    {
        return this.chunks.get(`${position.getFloorX() >> 4}:${position.getFloorZ() >> 4}`) ?? null;
    }

    getChunkAt(x, z)
    {
        return this.chunks.get(`${x}:${z}`) ?? null;
    }

    getChunks()
    {
        return this.chunks;
    }
}
module.exports = World;