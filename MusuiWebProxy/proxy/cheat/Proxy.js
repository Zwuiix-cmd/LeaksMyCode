const {ping, Relay} = require('bedrock-protocol');
const Path = require("path");
const MathJS = require("../utils/MathJS");
const Session = require("./session/Session");
const HOST = "127.0.0.1";

class Proxy
{
    initialRelay;
    relayData = {};
    player;

    constructor()
    {
    }

    async load(name, destHost, destPort)
    {
        /*let online;
        await ping({host: destHost, port: destPort}).then(async res => {
            online = true;
        }).catch(err => {
            online = false;
        });
        if(!online) {
            return false;
        }*/

        this.relayData = {
            host: HOST,
            port: MathJS.rand(1000, 50000),
            profilesFolder: Path.join(process.cwd() + "/proxy/others/"),
            destination: {
                host: destHost,
                port: destPort
            },
        }

        this.initialRelay = await new Relay(this.relayData);
        await this.initialRelay.listen();
        this.initialRelay.on('connect', player => {
            player.on('login', (packet) => {
                let info = `${player.connection.address}`.split("/");
                let session = new Session(player, packet.user.displayName, this);
                session.name = packet.user.displayName;
                session.xuid = packet.user.XUID;
                this.player = session;
            });
        });
        return true;
    }

    getHost()
    {
        return this.relayData.host;
    }

    getPort()
    {
        return this.relayData.port;
    }

    getDestHost()
    {
        return this.relayData.destination.host;
    }

    getDestPort()
    {
        return this.relayData.destination.port;
    }

    getPlayer()
    {
        return this.player;
    }
}
module.exports = Proxy;