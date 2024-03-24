const Path = require("path");
const fs = require("fs");
const Server = require("./server/Server");
const Express = require("./express/Express");
const Logger = require("./utils/Logger");

class App
{
    servers = [];
    pluginsStats = new Map();
    playersStats = [];
    pluginsTop = new Map();
    playersTop = new Map();

    constructor()
    {
        this.load().then(() => this.enable());
    }

    async load() {
        let serversPath = Path.join(process.cwd(), "resources", "servers.txt");
        let readServers = fs.readFileSync(serversPath, {encoding: "utf-8"});
        let serversLines = readServers.split(/\r?\n/);
        for (let i = 0; i < serversLines.length; i++) {
            if(serversLines[i] === "") {
                continue;
            }
            let host = `${serversLines[i]}`.split(":");
            this.servers.push(new Server(host[0], parseInt(host[1])));
        }

        setInterval(() => this.logData(), 1000 * 15);
    }

    enable()
    {
        this.servers.forEach(value => {
            value.enable();
        });

        Express.new(80, this);
    }

    async logData() {
        Logger.debug("logData");

        // PLUGINS STATS
        this.pluginsStats = new Map();
        this.playersStats = [];
        for (const value of this.servers) {
            this.playersStats[`${value.address}:${value.port}`] = value.players;
            let plugins = value.plugins;
            await plugins.forEach((plugin) => {
                if (this.pluginsStats.has(plugin.getName())) {
                    this.pluginsStats.set(plugin.getName(), this.pluginsStats.get(plugin.getName()) + 1);
                } else this.pluginsStats.set(plugin.getName(), 1);
            });
        }

        const pluginsStatsArray = Array.from(this.pluginsStats);
        pluginsStatsArray.sort((a, b) => b[1] - a[1]);
        pluginsStatsArray.forEach((value, index, array) => this.pluginsTop.set(value[0], Math.floor((value[1] / this.servers.length) * 100)));

        const serverStats = {};
        const totalPlayers = Object.values(this.playersStats).flat().length;
        Object.entries(this.playersStats).forEach(([server, players]) => serverStats[server] = { percentage: Math.floor((players.length / totalPlayers) * 100), players: players.length });

        const serverStatsArray = Object.entries(serverStats).map(([server, stats]) => ({ server, ...stats }));
        serverStatsArray.sort((a, b) => b.percentage - a.percentage);
        serverStatsArray.forEach(({ server, percentage, players }) => this.playersTop.set(server, {percentage: percentage, players: players}));
    }
}
new App();
