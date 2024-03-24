const axios = require("axios");
const Logger = require("../utils/Logger");
const Plugin = require('../plugin/Plugin');
const Query = require("../utils/Query");
const libquery = require("libquery");
class Server
{
    address;
    port;
    players = [];
    lastPlayers = [];
    plugins = [];
    playersOnlines = 0;

    constructor(address, port)  {
        this.address = address;
        this.port = port;
    }

    async enable() {
        const update = async () => {
            try {
                let data = await Query.query(this.address, this.port);
                this.onQuery(data);
            } catch (error) {
                this.onQueryFail(error.message)
            }
        };
        await update();
        setInterval(() => update(), 1000 * 60 * 5);
    }

    onQuery(res)
    {
        const isObject = (value) => {
            return value !== null && typeof value === 'object';
        }

        if(!isObject(res)) {
            Logger.error(typeof res);
            return;
        }

        let playersOnline = parseInt(res.online);
        if(isNaN(playersOnline) || playersOnline === 19132 || playersOnline) playersOnline = 0;
        this.playersOnlines = playersOnline;

        if(Array.isArray(res["players"])) {
            this.lastPlayers = [...this.players];
            this.players = res["players"];
        }

        let pluginsData = res.plugins;
        if(typeof pluginsData === 'string') {
            let tempPlugins = [];

            let split = pluginsData.split(": ");
            if(split.length < 1) {
                return;
            }

            let data = split[1].split("; ");
            for (let i = 0; i < data.length; i++) tempPlugins.push(new Plugin(data[i].split(" ")[0]));
            this.plugins = tempPlugins;
        } else  this.plugins = [];
    }

    onQueryFail(error)
    {
        //this.players = [];
        //this.plugins = [];
    }
}
module.exports = Server;
