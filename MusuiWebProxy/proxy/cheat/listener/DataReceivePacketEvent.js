const {fromObject, Vector3} = require("../../utils/math/Vector3");
const PlayerEntity = require("../entities/PlayerEntity");
const Item = require("../item/Item");
const ItemIdentifier = require("../item/ItemIdentifier");
const {createWriteStream} = require("fs");
const Path = require("path");
const MathJS = require("../../utils/MathJS");

class DataReceivePacketEvent
{
    onDataReceive(session, packet)
    {
        switch (packet.name) {
            case "move_player":
                if(packet.params.runtime_id == session.getId()) {
                    session.onGround = packet.params.on_ground;
                }
                break;
            case "player_auth_input":
                session.sendPosition(fromObject(packet.params.position));
                session.yaw = packet.params.yaw;
                session.pitch = packet.params.pitch;

                session.isSprinting = packet.params.input_data.sprinting;
                session.isJumping = packet.params.input_data.jumping;

                if(packet.params.input_data.start_jumping) {
                    session.onGround = false;
                }

                if(packet.params.input_data.up && !session.inputFlags.includes("w")) {
                    session.inputFlags.push("w");
                }else if(packet.params.input_data.down && !session.inputFlags.includes("s")) {
                    session.inputFlags.push("s");
                }else if(packet.params.input_data.left && !session.inputFlags.includes("q")) {
                    session.inputFlags.push("q");
                }else if(packet.params.input_data.right && !session.inputFlags.includes("d")) {
                    session.inputFlags.push("d");
                }

                /*
                {
  _value: 0n,
  ascend: false,
  descend: false,
  north_jump: false,
  jump_down: false,
  sprint_down: false,
  change_height: false,
  jumping: false,
  auto_jumping_in_water: false,
  sneaking: false,
  sneak_down: false,
  up: false,
  down: false,
  left: false,
  right: false,
  up_left: false,
  up_right: false,
  want_up: false,
  want_down: false,
  want_down_slow: false,
  want_up_slow: false,
  sprinting: false,
  ascend_block: false,
  descend_block: false,
  sneak_toggle_down: false,
  persist_sneak: false,
  start_sprinting: false,
  stop_sprinting: false,
  start_sneaking: false,
  stop_sneaking: false,
  start_swimming: false,
  stop_swimming: false,
  start_jumping: false,
  start_gliding: false,
  stop_gliding: false,
  item_interact: false,
  block_action: false,
  item_stack_request: false,
  handled_teleport: false,
  emoting: false,
  missed_swing: false,
  start_crawling: false,
  stop_crawling: false
}

                 */

                break;
            case "mob_equipment":
                if(packet.params.runtime_entity_id == session.getId() && packet.params.window_id === "inventory") {
                    session.hotbarSlot = packet.params.selected_slot;
                }
                break;
            case "interact":
                if(packet.params.action_id === "mouse_over_entity") {
                    let entity = session.playersEntities.get(packet.params.target_entity_id);
                    if(entity instanceof PlayerEntity) {
                        session.cursorEntity = {entity: entity, position: fromObject(packet.params.position)};
                    }
                }
                break;
            case "inventory_transaction":
                let transaction = packet.params.transaction;
                if(transaction.transaction_type === "normal") {
                    let actions = transaction.actions;
                    actions.forEach((action) => {
                        if(action.source_type === "container" && action.inventory_id === "inventory") {
                            let item = new Item(new ItemIdentifier(action.new_item.network_id, action.new_item.metadata), "Unknown");
                            item.setCount(action.new_item.count);
                            item.has_stack_id = action.new_item.has_stack_id;
                            item.block_runtime_id = action.new_item.block_runtime_id;
                            item.extra = action.new_item.extra;

                            session.getInventory().setItem(action.slot, item);
                        }
                    });
                }
                break;
            case "disconnect":
                session.isConnected = false;
                break;
            case "player_skin":
                break;
            default:

                break;
        }
        session.moduleManager.handlePacket("serverbound", packet);
    }
}
module.exports = DataReceivePacketEvent;