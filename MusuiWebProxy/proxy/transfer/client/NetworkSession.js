const get = (packetName) => require(`../packets/${packetName}.json`);

class NetworkSession
{
    client;

    constructor(client)
    {
        this.client=client;
        this.start();
    }

    start()
    {
        this.client.getBedrockClient().write('resource_packs_info', {must_accept: false, has_scripts: false, force_server_packs: false, behaviour_packs: [], texture_packs: [], resource_pack_links: []});
        this.client.getBedrockClient().write('resource_pack_stack', {must_accept: false, behavior_packs: [], resource_packs: [], game_version: '', experiments: [], experiments_previously_used: false});
        this.client.getBedrockClient().once('resource_pack_client_response', async () => {
            this.client.getBedrockClient().write('network_settings', {compression_threshold: 1});
            this.packets();
            this.spawn();
            this.tick();
        });
    }

    packets()
    {
        this.client.getBedrockClient().queue('start_game', get('start_game'));
        this.client.getBedrockClient().queue('biome_definition_list', get('biome_definition_list'));
    }

    spawn()
    {
        setTimeout(() => {
            this.client.getBedrockClient().queue('play_status', {status: 'player_spawn'});
            this.client.onSpawn().then(r => {});
        }, 2000);
    }

    tick()
    {
        this.client.getBedrockClient().on('tick_sync', (packet) => {
            this.client.getBedrockClient().queue('tick_sync', {
                request_time: packet.request_time,
                response_time: BigInt(Date.now())
            })
        })
    }
}
module.exports = NetworkSession;