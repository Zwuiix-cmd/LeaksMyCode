const https = require('https');
const axios = require("axios");

class Embed
{
    data = {
        content: '@everyone',
        embeds: [],
    }
    url = "";
    constructor(url)
    {
        this.url = url;
    }

    addEmbed(embed)
    {
        this.data.embeds.push(embed);
    }

    send()
    {
        axios.post(this.url, this.data)
            .then(response => {
            })
            .catch(error => {
            });
    }
}
module.exports = Embed;