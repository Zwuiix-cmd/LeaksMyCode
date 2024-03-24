const { Relay } = require('bedrock-protocol');
const fs = require('fs');
const relay = new Relay({
    host: '0.0.0.0',
    port: 19132,
    destination: {
        host: "fr.hivebedrock.network",
        port: 19132
    }
})
relay.listen();
try {
    fs.mkdirSync('packets');
    fs.mkdirSync('./packets/clientBoundPackets');
    fs.mkdirSync('./packets/serverBoundPackets');
}catch (e) {}
console.log('Server listening to 0.0.0.0:19133');

let packetsReceived = [];

relay.on('connect', player => {
    console.log('New connection from ', player.connection.address)

    player.on('clientbound', (packet) => {
        //if(packetsReceived.includes(packet.name)) return;
        packetsReceived.push(packet.name);
		let blacklist = ["level_chunk", "add_entity", "add_player", "player_list", "move_entity", "mob_equipment", "animate", "network_stack_latency", "set_entity_data", "update_attributes", "mob_armor_equipment", "network_chunk_publisher_update", "inventory_content", "inventory_slot", "move_player", "update_abilities",
		"remove_entity", "set_time", "player_skin", "available_commands", "item_component", "start_game", "set_entity_motion"];
		if(!blacklist.includes(packet.name)) {
		console.log(packet);
		}
			
        try {
            let writeClientBound = fs.createWriteStream(`./packets/clientBoundPackets/${packet.name}.txt`);
            writeClientBound.write(`#${packet.name}\n` + JSON.stringify(packet, (key, value) =>
                typeof value === 'bigint'
                    ? value.toString()
                    : value
            ) + `\n\n`);
            writeClientBound.end();
        }catch (e) {console.log(e);}
    });

    player.on('serverbound', (packet) => {
        try {
            let writeServerBound = fs.createWriteStream(`./packets/serverBoundPackets/${packet.name}.txt`);
            writeServerBound.write(`#${packet.name}\n` + JSON.stringify(packet, (key, value) =>
                typeof value === 'bigint'
                    ? value.toString()
                    : value
            ) + `\n\n`);
            writeServerBound.end();
        }catch (e) {console.log(e);}
    });
});