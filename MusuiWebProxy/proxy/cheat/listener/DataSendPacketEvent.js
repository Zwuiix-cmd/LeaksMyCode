const {fromObject, Vector3} = require("../../utils/math/Vector3");
const PlayerEntity = require("../entities/PlayerEntity");
const Vector2 = require("../../utils/math/Vector2");
const e = require("express");
const Item = require("../item/Item");
const ItemIdentifier = require("../item/ItemIdentifier");
const Block = require("../block/Block");
const assert = require("assert");
const registry = require('prismarine-registry')('bedrock_1.19.1');
const ChunkColumn = require('prismarine-chunk')(registry);

class DataSendPacketEvent
{
    onDataSend(session, packet) {
        switch (packet.name) {
            case "start_game":
                session.sendPosition(fromObject({
                    x: packet.params.player_position.x,
                    y: packet.params.player_position.y,
                    z: packet.params.player_position.z,
                    world: packet.params.world_name,
                    pitch: 0,
                    yaw: 0
                }));
                session.id = packet.params.runtime_entity_id;
                break;
            case "add_player":
                session.playersEntities.set(packet.params.runtime_id, new PlayerEntity(
                    packet.params.username,
                    packet.params.uuid,
                    packet.params.runtime_id,
                    fromObject(packet.params.position),
                    new Vector2(packet.params.yaw, packet.params.pitch),
                    packet.params.device_id,
                    packet.params.device_os,
                ));
                break;
            case "remove_entity":
                let entityId = packet.params.entity_id_self;
                if (session.playersEntities.has(entityId)) session.playersEntities.delete(entityId);
                break;
            case "move_entity":
                let runtime_id = packet.params.runtime_entity_id;

                let pEntity = session.playersEntities.get(runtime_id);
                if (pEntity instanceof PlayerEntity) {
                    pEntity.setPosition(fromObject(packet.params.position));
                    pEntity.rotation = new Vector2(packet.params.rotation.yaw, packet.params.rotation.pitch);
                }
                break;
            case "mob_equipment":
                if (packet.params.runtime_entity_id == session.getId() && packet.params.window_id === "inventory") {
                    if (packet.params.hotbarSlot === session.hotbarSlot) {
                        packet.params.runtime_entity_id = 32767;
                        return;
                    }
                    packet.params.hotbarSlot = session.hotbarSlot;
                    packet.params.slot = session.hotbarSlot;
                }
                break;
            case "play_status":
                session.onSpawn();
                //session.sendCameraPresets();
                break;
            case "camera_presets":
                break;
            case "inventory_content":
                if (packet.params.window_id === "inventory") {
                    let contents = new Map();

                    let i = 0;
                    packet.params.input.forEach((value) => {
                        let item = new Item(new ItemIdentifier(value.network_id, value.metadata), "Unknown");
                        item.setCount(value.count);
                        item.has_stack_id = value.has_stack_id;
                        item.block_runtime_id = value.block_runtime_id;
                        item.extra = value.extra;

                        contents.set(i, item);
                        i++;
                    });

                    session.getInventory().setContents(contents)
                }
                break;
            case "item_stack_response":
                packet.params.responses.forEach((response) => {
                    if (response.status === "ok") {
                        response.containers.forEach((value) => {
                            if (value.slot_type === "inventory") {
                                value.slots.forEach((itemResponse) => {
                                    let id = itemResponse.item_stack_id;
                                    let count = itemResponse.count;
                                    let slot = itemResponse.slot;
                                    let name = itemResponse.custom_name === "" ? "Unknown" : itemResponse.custom_name;
                                    let item = new Item(new ItemIdentifier(id, 0), name);
                                    item.setCount(count);

                                    session.getInventory().setItem(slot, item);
                                });
                            }
                        });
                    }
                });
                break;
            case "level_chunk":
                session.getWorld().pushChunk(packet.params.x, packet.params.z);
                let chunk = session.getWorld().getChunkAt(packet.params.x, packet.params.z);

                /*let chunkData = packet.params.payload.toJSON().data;
                let size = (16 * 256 * 16);
                for (let index = 0; index < size; index++) {
                    const x = index % 16;
                    const y = Math.floor(index / (16 * 16)) % 256;
                    const z = Math.floor(index / 16) % 16;
                    const id = chunkData[(y * 16 * 16 + z * 16 + x)] ?? 0;

                    chunk.setBlock(new Vector3(x, y, z), new Block(id, {}, 0));
                }*/

                break;
            case "update_block":
                let params = packet.params;
                session.getWorld().setBlockAt(fromObject(params.position), new Block(params.block_runtime_id, params.flags, params.layer))
                break;
            case "disconnect":
                session.isConnected = false;
                break;
            default:
                break;
        }
        session.moduleManager.handlePacket("clientbound", packet);
    }
}
module.exports = DataSendPacketEvent;