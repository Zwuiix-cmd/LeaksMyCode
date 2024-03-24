const ItemIdentifier = require("./ItemIdentifier");

class Item
{
    itemIdentifier;
    name;
    id;
    count = 1;
    has_stack_id = 0;
    block_runtime_id = 0;
    nbt = {type: "compound", name: "", value: {}};
    extra = {
        has_nbt: true,
        nbt: {
            version: 1,
            nbt: this.nbt
        },
        can_place_on: [],
        can_destroy: []
    };

    /**
     *
     * @param itemIdentifier {ItemIdentifier}
     * @param name {string}
     */
    constructor(itemIdentifier, name)
    {
        this.itemIdentifier = itemIdentifier;
        this.name = name;
    }

    getName()
    {
        return this.name;
    }

    getId()
    {
        return this.itemIdentifier.getId();
    }

    getMeta()
    {
        return this.itemIdentifier.getMeta();
    }

    getCount()
    {
        return this.count;
    }

    setCount(count)
    {
        this.count = count;
    }

    asObject()
    {
        return {
            network_id: this.getId(),
            count: this.getCount(),
            metadata: this.getMeta(),
            has_stack_id: this.has_stack_id,
            block_runtime_id: this.block_runtime_id,
            extra: this.extra
        };
    }
}
module.exports = Item;