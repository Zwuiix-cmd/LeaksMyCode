const expressWebApp = require("express");
const app = expressWebApp();
const bodyParser = require("body-parser");
const Query = require("../utils/Query");
const Plugin = require("../plugin/Plugin");
const axl = require("app-xbox-live");
const Path = require("path");
const fs = require("fs");
const { ping } = require('bedrock-protocol');
const bedrock = require('bedrock-protocol');
const { createCanvas, loadImage, ImageData } = require('canvas');

class Express
{
    server;

    static new(port, server) { return new Express(port, server); }

    constructor(port, server)
    {
        this.server = server;
        app.listen(port, "0.0.0.0", () => this.load());
    }

    async load() {
        const token = await axl.Token("cunhaafolias@hotmail.com", "lE9koG25");
        const xl = await new axl.Account(`XBL3.0 x=${token[1]};${token[0]}`);

        app.use(bodyParser.urlencoded({extended: true}))
        app.use(bodyParser.json());

        app.get('/api/@_x1YZ/:address/:port', (req, res) => {
            const address = req.params.address;
            const port = req.params.port;
            ping({ host: address, port: parseInt(port) }).then(ress => {
                let bot = bedrock.createClient({host: address, port: parseInt(port), skipPing: true, offline: false, username: "Bot", profilesFolder: Path.join(process.cwd() + "/resources/token"), connectTimeout: 2500});
                bot.on("disconnect", (value) => {
                    res.send(value.message);
                });
                bot.on("player_list", (packet) => {
                    packet.records.records.forEach((value) => {
                        let username = value.username.toLowerCase().replaceAll(" ", "_");
                        const buffer = value.skin_data.skin_data.data;
                        const width = value.skin_data.skin_data.width;
                        const height = value.skin_data.skin_data.height;
                        const canvas = createCanvas(width, height);
                        const ctx = canvas.getContext('2d');

                        const imgData = ctx.createImageData(width, height);
                        for (let i = 0; i < buffer.length; i++) imgData.data[i] = buffer[i];
                        ctx.putImageData(imgData, 0, 0);
                        fs.writeFileSync(Path.join(process.cwd(), "resources", "skins", `${username}.png`), canvas.toBuffer('image/png'));
                    });
                    bot.close();
                    res.send(`success ${packet.records.records.length} players`);
                });
            }).catch(e => res.send(e.message));
        });

        app.get('/api/skin/:user', async (req, res) => {
            let user = req.params.user.toLowerCase();
            let imagePath = Path.join(process.cwd(), "resources", "skins", `${user}.png`);
            fs.readFile(imagePath, (err, data) => {
                if (err) {
                    res.status(500).send('Error while reading the image');
                } else {
                    res.contentType('image/png');
                    res.send(data);
                }
            });
        });

        app.get('/api/skin/:user/head', async (req, res) => {
            let user = req.params.user.toLowerCase();
            let imagePath = Path.join(process.cwd(), "resources", "skins", `${user}.png`);

            fs.readFile(imagePath, (err, data) => {
                if (err) {
                    res.status(500).send('Error while reading the image');
                } else {
                    const Jimp = require('jimp');

                    Jimp.read(data, (err, image) => {
                        if (err) {
                            res.status(500).send('Error while processing the image');
                        } else {
                            image.crop(8, 8, 8, 8);

                            image.getBuffer(Jimp.MIME_PNG, (err, buffer) => {
                                if (err) {
                                    res.status(500).send('Error while converting the image');
                                } else {
                                    res.contentType('image/png');
                                    res.send(buffer);
                                }
                            });
                        }
                    });
                }
            });
        });

        app.get(`/api/stats/plugins`, (req, res) => {
            if (!(this.server.pluginsTop instanceof Map)) {
                console.error("Unexpected format for this.server.pluginsTop");
                res.status(500).send("Internal Server Error");
                return;
            }

            const formattedData = {};
            this.server.pluginsTop.forEach((count, plugin) => formattedData[plugin] = `${count}`);

            res.json(formattedData);
        });

        app.get(`/api/stats/players`, (req, res) => {
            let formattedData = {};
            this.server.playersTop.forEach((value, key, map) => formattedData[key] = value);
            res.json(formattedData);
        });

        app.get(`/api/query/:address/:port`, async (req, res) => {
            try {
                let data = await Query.query(req.params.address, parseInt(req.params.port));
                res.json(data);
            } catch (error) {
                res.status(666).send(error);
            }
        });

        app.get('/api/xbox/xuid/:gamertag', async (req, res) => {
            let user = req.params.gamertag.toLowerCase();
            xl.people.find(user, 1).then(e => res.send(e.people[0].xuid)).catch(err => res.send(err.message));
        });

        app.get('/api/xbox/profile/:gamertag', async (req, res) => {
            let user = req.params.gamertag.toLowerCase();
            xl.people.find(user, 1).then(e => {
                let account = e.people[0];
                let xuid = account.xuid;
                let avatar = account.displayPicRaw;
                let gamerTag = account.gamertag;
                let gamerScore = account.gamerScore;
                let xboxOneRep = account.xboxOneRep;

                let formatedData = {
                    xuid: xuid,
                    avatar: avatar,
                    gamerTag: gamerTag ,
                    gamerScore: gamerScore,
                    level: xboxOneRep,
                };

                xl.people.get(xuid).then(r => {
                    let ac = r.people[0];
                    let presenceState = ac.presenceState ?? null;
                    let presenceText = ac.presenceText ?? null;

                    let detail = account.detail;
                    formatedData["status"] = `${presenceState} (${presenceText})`;
                    formatedData["details"] = {
                        bio: detail.bio ?? "none",
                        tier: detail.accountTier,
                        gamepass: detail.hasGamePass ? "yes" : "no",
                        followerCount: detail.followerCount,
                        followingCount: detail.followingCount,
                    };

                    xl.people.games.get(xuid).then(f => {
                        let games = [];
                        let minecraft = [];

                        let reformatDate = (game) => {
                            let date = `${game.titleHistory.lastTimePlayed}`.split("T")[0];
                            let split = date.split("-");

                            return `${split[2]}-${split[1]}-${split[0]}`;
                        };

                        f.titles.forEach((game) => {
                            if(game.name === "Minecraft") {
                                games.push(`- ${game.name} (${reformatDate(game)})`);
                            }else if(game.name.includes("Minecraft")) {
                                minecraft.push(`${game.name}`.split(" ").slice(1).join(" ").replaceAll("for ", ""));
                            } else games.push(`${game.name} (${reformatDate(game)})`);
                        });
                        games.push(`Minecraft (${minecraft.join(", ")})`);
                        formatedData["games"] = games;
                        res.json(formatedData);
                    }).catch(err => res.send(err.message));
                }).catch(err => res.send(err.message));
            }).catch(err => res.send(err.message));
        });

        app.get(`/query/:address/:port`, async (req, res) => {
            try {
                const data = await Query.query(req.params.address, parseInt(req.params.port));
                let plugins = [];

                let split = data["plugins"].split(": ");
                if (split.length > 1) {
                    let dataPlugins = split[1].split("; ");
                    for (let i = 0; i < dataPlugins.length; i++) plugins.push(dataPlugins[i])
                }

                if (plugins.length === 0) plugins.push("Query off/none");
                if (data["players"].length === 0) data["players"] = ["Query off/none"];

                const htmlResponse = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body {
                        font-family: 'Arial', sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        margin: 20px;
                    }
                    .container {
                        max-width: 800px;
                        margin: auto;
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        color: #007BFF;
                        margin-bottom: 10px;
                    }
                    h2 {
                        color: #28A745;
                        margin-top: 0;
                    }
                    ul {
                        list-style: none;
                        padding: 0;
                    }
                    li {
                        margin-bottom: 5px;
                    }
                    .players-list {
                        display: flex;
                        flex-wrap: wrap;
                    }
                    .players-list-item {
                        margin-right: 10px;
                    }
                    .plugins-list {
                        margin-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>${req.params.address}:${req.params.port}</h1>
                    <h2>${data.motd}</h2>
                    <ul>
                        <li>Host: ${data.host}</li>
                        <li>Port: ${data.port}</li>
                        <li>Version: ${data.version}</li>
                        <li>Software: ${data.software}</li>
                        <li>Online Players: ${data.online}</li>
                        <li>Max Players: ${data.max}</li>
                    </ul>
                    <h2>Plugins:</h2>
                    <ul class="plugins-list">
                        ${plugins.map(plugin => `<li>${plugin}</li>`).join('')}
                    </ul>
                    <h2>Players:</h2>
                    <ul class="players-list">
                        ${data.players.map(player => `<li class="players-list-item">${player}</li>`).join('')}
                    </ul>
                </div>
            </body>
            </html>
        `;

                res.setHeader('Content-Type', 'text/html');
                res.send(htmlResponse);
            } catch (error) {
                res.status(500).send(error.message);
            }
        });

    }
}
module.exports = Express;
