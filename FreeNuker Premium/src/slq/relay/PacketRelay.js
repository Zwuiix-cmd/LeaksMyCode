const Vector2 = require("../utils/Vector2");
let config = require("../../../resources/config.json");
class PacketRelay
{
    player;
    proxy;
    id = 0;
    toggle = false;
    position;
    yaw;
    pitch;
    gamemode = 0;
    nuking = false;
    packetSended = 0;

    constructor(player, proxy) {
        this.player = player;
        this.proxy = proxy;
        this.start();
    }

    breakRegularity()
    {
        this.sendToServer('tick_sync', { request_time: 0, response_time: 0n }) // Breaking packet regularity
        setTimeout(() => this.breakRegularity, Math.floor(Math.random() * 4000) + 1000);
    }

    start()
    {
        this.player.on('clientbound', ({ name, params }, des) => {
            if(name === "start_game") {
                this.id =  params.runtime_entity_id;
                this.position = params.player_position;
                setInterval(() => {
                    this.syncGamemode();
                }, 2500);
            } else if(name === "set_player_game_type") {
                switch (params.gamemode) {
                    case "creative":
                        this.gamemode = 1;
                        break;
                    default:
                        this.gamemode = 0;
                        break;
                }
            } else if(name === "mob_equipment") {
                if(params.runtime_entity_id === this.id) {
                    des.canceled = true;
                }
            }
        });

        let animatePacket = 0;
        let playerAuthInputPacket = 0;
        setInterval(() => {
            animatePacket = 0;
            playerAuthInputPacket = 0;

            this.sendTip(`§3PPS: ${this.packetSended}\nNuking: ${this.nuking ? "Oui" : "Non"}`);
            this.packetSended = 0;
        }, 1050);

        this.player.on('serverbound', async ({name, params}, des) => {
            if(this.nuking) {
                des.canceled = true;
            }

            if(name === "set_local_player_as_initialized") {
                this.breakRegularity();
            }

            if(name === "mob_equipment") {
                des.canceled = true;
            } else if(name === "animate") {
                animatePacket++;
                if(animatePacket > 1) des.canceled = true; // 1 PACKET PER FPS (ex: 200hz => 200 packet)
            } else if (name === 'text') {
                let message = `${params.message}`;
                if (!message.startsWith("slq?nuker")) return;
                des.canceled = true;
                this.toggle = !this.toggle;
                this.sendMessage("§3[§bFreeNuker§3] §aVous avez §3" + (this.toggle ? "Activé" : "Désactivé") + " §ale §3Nuker§a.");
            } else if (name === "player_auth_input") {
                playerAuthInputPacket++;

                if(this.toggle && playerAuthInputPacket > 1) des.canceled = true;

                this.position = params.position;
                this.yaw = params.yaw;
                this.pitch = params.pitch;

                if (params.transaction !== undefined && this.toggle && !this.nuking) {
                    let data = params.transaction.data;
                    if (data.action_type === "break_block") {
                        let blockPosition = data.block_position;

                        let gamemode = this.getGamemode() === 1;
                        let value = gamemode ? 13 : this.proxy.radius;

                        let newPos = blockPosition;
                        let minX = newPos.x - value;
                        let maxX = newPos.x + value;
                        let minY = newPos.y - value;
                        let maxY = newPos.y + value;
                        let minZ = newPos.z - value;
                        let maxZ = newPos.z + value;

                        if(minY < 0) minY = 0;
                        if(maxY > 255) maxY = 255;

                        let bypass = this.proxy.bypass;

                        let i = 0;
                        let start = Date.now();
                        for (let x = minX; x <= maxX; x++) {
                            for (let z = minZ; z <= maxZ; z++) {
                                for (let y = minY; y <= maxY; y++) {
                                    if(bypass) {
                                        this.nuking = true;
                                        this.leftClickBlock({x: x, y: y, z: z}); // Plugin AntiInstantBreak require left click in block
                                        await new Promise(resolve => setTimeout(resolve, config.timeToBreak)); // Kick for too many badpacket in batch
                                        this.breakBlock(this.position, {x: x, y: y, z: z});

                                        i++;
                                    } else {
                                        if ((i % 90) === 0 && i !== 0) await new Promise(resolve => setTimeout(resolve, gamemode ? 1000 : 250));

                                        if ((i % 4) === 0) {
                                            this.breakInventory(this.position, {x: x, y: y, z: z});
                                        } else this.breakBlock(this.position, {x: x, y: y, z: z});

                                        i++;
                                        this.nuking = true;
                                    }
                                }
                            }
                        }

                        setTimeout(() => (this.nuking = false), 50);
                        this.sendMessage(`§3[§bFreeNuker§3] §aVous avez casser §3${i} blocs§a en §3` + ((Date.now() - start) / 1000) + `seconde(s)§a.`);
                    }
                }
            }
        })
    }

    syncGamemode()
    {
        this.sendToServer("set_player_game_type", {
            gamemode: 6
        });
    }

    getGamemode()
    {
        return this.gamemode;
    }

    /**
     * Send message to player
     * @param string {string}
     */
    sendMessage(string)
    {
        this.player.queue('text', {
            type: "chat",
            needs_translation: false,
            source_name: '',
            xuid: '',
            platform_chat_id: '',
            message: string
        });
    }

    /**
     * Send message to player
     * @param string {string}
     */
    sendTip(string)
    {
        this.player.queue('text', {
            type: "tip",
            needs_translation: false,
            source_name: '',
            xuid: '',
            platform_chat_id: '',
            message: string
        });
    }


    leftClickBlock(position)
    {
        this.sendToServer("player_action", {
            runtime_entity_id: this.id,
            action: "start_break",
            position: position,
            result_position: position,
            face: 0 << 1 | 1
        });
    }

    breakInventory(ppos, position)
    {
        this.sendToServer('inventory_transaction', {
            transaction: {
                legacy: {legacy_request_id: 0, legacy_transactions: undefined},
                transaction_type: 'item_use',
                actions: [],
                transaction_data: {
                    action_type: 'break_block',
                    block_position: position,
                    face: 0,
                    hotbar_slot: 0,
                    held_item: {network_id: 0},
                    player_pos: ppos,
                    click_pos: {x: 0, y: 0, z: 0},
                    block_runtime_id: 0
                }
            }
        });
    }

    breakBlock(ppos, position)
    {
        let direction = this.calculateAgreedDirection(position, ppos);
        this.sendToServer("player_auth_input", {
            pitch: direction.getZ(),
            yaw: direction.getX(),
            position: ppos,
            move_vector: { x: 0, z: 0 },
            head_yaw: direction.getX(),
            input_data: {
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
                item_interact: true,
                block_action: true,
                item_stack_request: false,
                handled_teleport: false,
                emoting: false,
                missed_swing: false,
                start_crawling: false,
                stop_crawling: false
            },
            input_mode: 'mouse',
            play_mode: 'screen',
            interaction_model: 'touch',
            gaze_direction: undefined,
            tick: 0n,
            delta: { x: 0, y: 0, z: 0 },
            transaction: {
                legacy: { legacy_request_id: 0, legacy_transactions: undefined },
                actions: [],
                data: {
                    action_type: "break_block",
                    block_position: position,
                    face: 0,
                    hotbar_slot: 0,
                    held_item: {network_id: 0},
                    player_pos: ppos,
                    click_pos: {x: 0, y: 0, z: 0},
                    block_runtime_id: 0
                }
            },
            item_stack_request: undefined,
            block_action: [],
            analogue_move_vector: { x: 0, z: 0 }
        });
    }

    sendToServer(name, params)
    {
        this.packetSended++;
        if(this.packetSended > 250) return;
        this.player.upstream.queue(name, params);
    }

    calculateAgreedDirection(pos, newPos)
    {
        let xdiff = pos.x - newPos.x;
        let zdiff = pos.z - newPos.z;
        let angle = Math.atan2(zdiff, xdiff);
        let yaw = ((angle * 180) / 3.1415926535898) - 90;
        let ydiff = pos.y - newPos.y;
        let v = new Vector2(newPos.x, newPos.z);
        let dist = v.distance(new Vector2(pos.x, pos.z));
        angle = Math.atan2(dist, ydiff);
        let pitch = ((angle * 180) / 3.1415926535898) - 90;

        return new Vector2(yaw, pitch);
    }
}
module.exports = PacketRelay;