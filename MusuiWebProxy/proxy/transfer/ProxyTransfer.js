const bedrock = require('bedrock-protocol');
const { Server } = require('bedrock-protocol');

const {getInstance} = require("./Server");
const EClient = require("./client/EClient");

class ProxyTransfer
{
    constructor()
    {
        this.start().then(r => {});
    }

    async start() {
        const server = new Server({
            host: "127.0.0.1",
            port: 1000,
            version: "1.20.40",
            maxPlayers: 9999,
            motd: {
                motd: "Dedicated Server",
                levelName: "Dedicated Server"
            }
        })
        await server.listen();
        await console.log('Proxy server started.');

        server.on('connect', client => {
            client.on('join', () => {
                getInstance().addClient(new EClient(client));
            });
        });
    }
}
module.exports = ProxyTransfer;