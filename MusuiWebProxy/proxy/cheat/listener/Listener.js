const DataReceivePacketEvent = require("./DataReceivePacketEvent");
const DataSendPacketEvent = require("./DataSendPacketEvent");

class Listener
{
    session;
    receiveEvent;
    sendEvent;

    constructor(session)
    {
        this.session = session;

        this.receiveEvent = new DataReceivePacketEvent();
        this.sendEvent = new DataSendPacketEvent();

        this.init();
    }

    init()
    {
        this.session.getBedrockPlayer().on('serverbound', ({ name, params }, des) => {
            this.receiveEvent.onDataReceive(this.session, {name: name, params: params, data: des});
        });
        this.session.getBedrockPlayer().on('clientbound', ({ name, params }, des) => {
            this.sendEvent.onDataSend(this.session, {name: name, params: params, data: des});
        });
    }
}
module.exports = Listener;