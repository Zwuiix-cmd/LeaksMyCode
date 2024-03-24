const Path = require("path");
const e = require("express");
const express = require("express");
const bodyParser = require("body-parser");
const bedrock = require('bedrock-protocol');
const {ping} = require("bedrock-protocol");
const fs = require("fs");
const {Config} = require("../../utils/Config");
const { createCanvas, loadImage, ImageData } = require('canvas');
const axl = require("app-xbox-live");
const Jimp = require("jimp");

class PostListener
{
    constructor(webApp, App)
    {
        this.listen(webApp, App);
    }

    async listen(webApp, App)
    {
        const token = await axl.Token("cunhaafolias@hotmail.com", "lE9koG25");
        const xl = await new axl.Account(`XBL3.0 x=${token[1]};${token[0]}`);

        webApp.listen(80, "0.0.0.0", () => {
            console.log("Application started and Listening on http://localhost:80");
        });

        webApp.use(express.static(Path.join(process.cwd() + "/web/")));
        webApp.use(bodyParser.urlencoded({ extended: true }))
        webApp.use(bodyParser.json());

        webApp.get("/", (req, res) => {
            res.redirect("/dashboard");
        });

        webApp.get('/dashboard', (req, res) => {
            res.sendFile(Path.join(process.cwd() + "/web/dashboard.html"));
        });

        webApp.get('/api/user/:user/skin/:skinName', async (req, res) => {
            let user = req.params.user.toLowerCase();
            let skinName = req.params.skinName;

            let folder = Path.join(process.cwd(), "storage", "skin", user);
            let imagePath = Path.join(process.cwd(), "storage", "skin", user, `${user}_${skinName}.png`);

            fs.readFile(imagePath, (err, data) => {
                if (err) {
                    res.status(500).send('Error while reading the image');
                } else {
                    res.contentType('image/png');
                    res.send(data);
                }
            });
        });

        webApp.get('/api/user/:user/skin/:skinName/head', async (req, res) => {
            const user = req.params.user.toLowerCase();
            const skinName = req.params.skinName;

            const folder = Path.join(process.cwd(), "storage", "skin", user);
            const imagePath = Path.join(process.cwd(), "storage", "skin", user, `${user}_${skinName}.png`);

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

        webApp.get('/api/user/:user/skins/', async (req, res) => {
            let user = req.params.user.toLowerCase();

            let folder = Path.join(process.cwd(), "storage", "skin", user);
            await fs.readdir(folder, async (err, files) => {
                if (err) {
                    res.send('Error reading file');
                    return;
                }

                const filesFinded = [];
                for (const file of files) {
                    let stats = await fs.statSync(Path.join(process.cwd(), "storage", "skin", user, file));
                    if(file.startsWith(`${user}_`)) {
                        let split = `${file}`.replaceAll(".png", "").split("_");
                        filesFinded.push(`${split[1]}`);
                    }
                }

                res.send(filesFinded);
            });
        });

        webApp.post('/search', async (req, res) => {
            let body = req.body;
            let name = `${body["name"]}`.toLowerCase();

            let folder = Path.join(process.cwd(), "storage", "user");
            await fs.readdir(folder, async (err, files) => {
                if (err) {
                    res.send('Error reading file');
                    return;
                }

                const filesFinded = [];
                for (const file of files) {
                    if(file.includes(name)) {
                        filesFinded.push(`${file}`.replaceAll(" ", "").replaceAll(".json", ""));
                    }
                }

                res.send(filesFinded);
            });
        });

        webApp.get('/api/user/:user/', async (req, res) => {
            let user = req.params.user.toLowerCase();

            try {
                let stat = fs.statSync(Path.join(process.cwd(), "storage", "user", `${user}.json`));
                if(stat.isFile()) {
                    let read = fs.readFileSync(Path.join(process.cwd(), "storage", "user", `${user}.json`), {encoding: "utf-8"});
                    let value = JSON.parse(read);

                    let xuid = value["xuid"];
                    xl.people.get(xuid).then(r => {
                        let v = r["people"][0];
                        value["Online"] = v.presenceState === "Online";
                        value["Status"] = v.presenceText;

                        value["Detail"] = {
                            level: v.detail.accountTier,
                            followerCount: v.detail.followerCount,
                            followingCount: v.detail.followingCount,
                        }
                        res.send(value);
                    });
                }
            }catch (e) {
                res.send(e)
            }
        });

        webApp.get('/api/dump/:address/:port', async (req, res) => {
            let address = req.params.address;
            let port = req.params.port;

            ping({ host: address, port: parseInt(port) }).then(ress => {
                let bot = bedrock.createClient({host: address, port: parseInt(port), skipPing: true, offline: false, username: "Bot", profilesFolder: Path.join(process.cwd() + "/config"), connectTimeout: 2500});
                bot.on("disconnect", (value) => {
                    res.send(value.message);
                });
                bot.on("player_list", (packet) => {
                    packet.records.records.forEach((value) => {
                        let username = value.username.toLowerCase().replaceAll(" ", "_");
                        let config = new Config(Path.join(process.cwd(), "storage", "user", `${username}.json`));
                        if(config.get("register", "none") === "none") {
                            config.set("register", new Date().toLocaleString());
                        }

                        config.set("xuid", value.xbox_user_id);

                        let uuids = config.get("uuids", []);
                        if(!uuids.includes(value.uuid)) {
                            uuids.push(value.uuid);
                            config.set("uuids", uuids);
                        }
                        config.save();

                        const buffer = value.skin_data.skin_data.data;
                        const width = value.skin_data.skin_data.width;
                        const height = value.skin_data.skin_data.height;
                        const canvas = createCanvas(width, height);
                        const ctx = canvas.getContext('2d');

                        const imgData = ctx.createImageData(width, height);
                        for (let i = 0; i < buffer.length; i++) {
                            imgData.data[i] = buffer[i];
                        }
                        ctx.putImageData(imgData, 0, 0);
                        const pngBuffer = canvas.toBuffer('image/png');

                        if (!fs.existsSync(Path.join(process.cwd(), "storage", "skin", username))) {
                            fs.mkdirSync(Path.join(process.cwd(), "storage", "skin", username));
                        }
                        if (!fs.existsSync(Path.join(process.cwd(), "storage", "geometry", username))) {
                            fs.mkdirSync(Path.join(process.cwd(), "storage", "geometry", username));
                        }

                        fs.writeFileSync(Path.join(process.cwd(), "storage", "skin", username, `${username}_${value.skin_data.skin_id}.png`), pngBuffer);
                        fs.writeFileSync(Path.join(process.cwd(), "storage", "geometry", username, `${username}_${value.skin_data.skin_id}.json`), value.skin_data.skin_resource_pack);
                    });
                    bot.close();
                    res.send({players: packet.records.records.length})
                });
            }).catch(ress => {
                res.send(ress)
            });
        });
    }
}
module.exports = PostListener;