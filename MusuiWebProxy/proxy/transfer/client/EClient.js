const NetworkSession = require("./NetworkSession");
const CustomForm = require("../form/CustomForm");
const ModalFormRequestPacket = require("../packet/ModalFormRequestPacket");
const FormID = require("../form/FormID");
const PacketListener = require("../listener/PacketListener");
const Code = require("../../utils/Code");
const PlayersHandler = require("../../others/PlayersHandler");
const AccountsHandler = require("../../others/AccountsHandler");

class EClient
{
    client;
    name = "";
    xuid = "";
    identity = "";
    titleId = "";
    userData;
    networkSession;
    isConnected = false;
    isTransfered = false;

    constructor(client)
    {
        this.client=client;
        this.userData=client.getUserData();
        this.name=this.userData.displayName;
        this.xuid=this.userData.XUID;
        this.identity=this.userData.identity;
        this.titleId=this.userData.titleId;
        this.networkSession = new NetworkSession(this);
    }

    getBedrockClient()
    {
        return this.client;
    }

    getName()
    {
        return this.name;
    }


    async onSpawn()
    {
        this.isConnected = true;

        if(!AccountsHandler.getInstance().canUseWithUsername(this.getName())) {
            console.log("aa")
            this.disconnect("§cSorry, you are not authorized to connect to this server!");
            return;
        }
    }

    disconnect(reason)
    {
        if(!this.isConnected) return;
        this.isConnected = false;
        this.client.queue('disconnect', {
            hide_disconnect_reason: false,
            message: reason
        })
    }

    transfer(address, port)
    {
        if(!this.isConnected) return;
        this.client.queue('transfer', {
            server_address: `${address}`,
            port: parseInt(`${port}`)
        });
    }

    sendMessage(str)
    {
        if(!this.isConnected) return;
        this.client.queue('text', {
            type: "chat",
            needs_translation: false,
            source_name: '',
            xuid: '',
            platform_chat_id: '',
            message: str
        });
    }
}
module.exports = EClient;