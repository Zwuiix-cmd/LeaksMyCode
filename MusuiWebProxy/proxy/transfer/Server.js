let instance;
class Server
{
    clients = new Map();

    constructor()
    {
    }

    /**
     *
     * @param client
     */
    addClient(client)
    {
        this.clients.set(client.getName(), client);
    }

    getClientByName(name)
    {
        return this.clients.get(name);
    }

    /**
     *
     * @param client
     */
    async removeClient(client) {
        delete (this.clients.delete(client.getName()));
    }
}
module.exports = {
    getInstance()
    {
        return instance ? instance : (instance = new Server());
    }, ProxyServer: Server
};