const Module = require('../Module');
const {fromObject, Vector3} = require("../../../utils/math/Vector3");
class ClickTP extends Module
{
    constructor(session) {
        super(session, "ClickTP", {});
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "inventory_transaction") {
            return;
        }

        if(packet.params.transaction.transaction_type === "item_use" && packet.params.transaction.transaction_data.action_type === "click_block") {
            this.session.move(fromObject(packet.params.transaction.transaction_data.block_position).addVector(new Vector3(0, 1, 0)));
        }
    }
}
module.exports = ClickTP;