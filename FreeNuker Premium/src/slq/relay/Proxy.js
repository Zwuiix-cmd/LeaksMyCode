const { Relay } = require('bedrock-protocol');
const PacketRelay = require("./PacketRelay");
const Path = require("path");
class Proxy
{
    host;
    port;
    radius;
    bypass;

    constructor(host, port, radius, bypass)
    {
        this.host = host;
        this.port = port;
        this.radius = radius;
        this.bypass = bypass;
        this.start();
    }

    start()
    {
        const options = {};
        options["host"] = "0.0.0.0";
        options["port"] = 19132;
        options["profilesFolder"] = Path.join(process.cwd() + '/players');
        options["destination"] = {
            host: this.host,
            port: this.port
        };

        const relay = new Relay(options);
        relay.listen().then(r => relay.on('connect', player => new PacketRelay(player, this)));
    }
}
module.exports = Proxy;