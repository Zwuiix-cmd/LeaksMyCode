const Item = require("../item/Item");
const ItemIdentifier = require("../item/ItemIdentifier");

class PlayerInventory
{
    session;
    inventory = new Map();

    constructor(session)
    {
        this.session = session;

        for(let i = 0; i <= 35; i++) {
            this.setItem(i, new Item(new ItemIdentifier(0, 0), "Air"));
        }
    }

    getItem(slot)
    {
        return this.inventory.get(slot);
    }

    setItem(slot, item)
    {
        this.inventory.set(slot, item);
    }

    getItemInHand()
    {
        return this.getItem(this.session.hotbarSlot);
    }

    setItemInHand(item)
    {
        this.setItem(this.session.hotbarSlot, item);
    }

    getContents()
    {
        return this.inventory;
    }

    setContents(arrayMap)
    {
        this.inventory = arrayMap;
    }
}
module.exports = PlayerInventory;