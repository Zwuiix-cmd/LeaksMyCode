const { ping } = require('bedrock-protocol');
const fs = require("fs");
const Path = require("path");
const Embed = require("./Embed");

const webhookUrl = 'WEBHOOK';

const ADDRESS = "play.endiorite.fr";
let PORT = 19100;

let validedPort = [];
let invalidedPort = [];

setInterval(() => {
    console.clear();
    console.log(`Port ouvert(s) (${validedPort.length}/${invalidedPort.length}): ${validedPort.join(", ")}`);
    //console.log(`Port fermé(s) (${invalidedPort.length}): ${invalidedPort.join(", ")}`);
    let aa = PORT;
    let a = ping({ host: ADDRESS, port: aa }).then(res => {
        validedPort.push(aa);
        fs.writeFileSync(Path.join(process.cwd(), "logs", `${aa}`), JSON.stringify(res));

        let fields = [
            { name: 'SoftWare', value: `${res.levelName}`, inline: true },
            { name: 'Motd', value: `${res.motd}`, inline: true },
            { name: 'Online', value: `${res.playersOnline}`, inline: true },
            { name: 'Version', value: `${res.version}`, inline: true },

        ];

        let embed = new Embed(webhookUrl);
        embed.addEmbed({
            description: `Ping positif: **${ADDRESS}**:**${aa}**`,
            color: 0x313338,
            fields: fields
        });
        embed.send();
    }).catch(e => {
        invalidedPort.push(aa);
    });
    PORT++;
}, 0.1);