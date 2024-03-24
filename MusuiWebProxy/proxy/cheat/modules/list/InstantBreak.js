const Module = require('../Module');
const {Vector3, fromObject} = require("../../../utils/math/Vector3");
const ItemBasic = require("../../item/BasicItem");
class InstantBreak extends Module
{
    constructor(session) {
        super(session, "InstantBreak", {});
    }
    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        if(packet.params.block_action !== undefined) {
            let blockAction = packet.params.block_action;
            blockAction.forEach((value) => {
               if(value.action === "start_break") {
                   this.session.breakBlock(fromObject(value.position));
               }
            });
        }
    }
}
module.exports = InstantBreak;