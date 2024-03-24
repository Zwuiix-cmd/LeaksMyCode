class ItemIdentifier
{
    id = 0;
    meta = 0;

    /**
     *
     * @param id {number}
     * @param meta {number}
     */
    constructor(id, meta)
    {
        this.id = id;
        this.meta = meta;
    }

    getId()
    {
        return this.id;
    }

    getMeta()
    {
        return this.meta;
    }
}
module.exports = ItemIdentifier;