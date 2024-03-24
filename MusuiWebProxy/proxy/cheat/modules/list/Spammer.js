const Module = require('../Module');
const Code = require("../../../utils/Code");
const MathJS = require("../../../utils/MathJS");
class Spammer extends Module
{
    constructor(session) {
        super(session, "Spammer", {message: "Musui - Web Client: discord.gg/KhWnNWmgCs"});
    }

    onUpdate(data)
    {
        this.flags.message = data.spammer_message === '' ? "Musui - Web Client: discord.gg/KhWnNWmgCs" : data.spammer_message;
    }

    onTick()
    {
        let code = Code.generate(MathJS.rand(6, 12));
        for (let i = 0; i < MathJS.rand(1, 5); i++) {
            this.session.chat(this.flags.message + ` | #${code}`);
        }
    }
}
module.exports = Spammer;