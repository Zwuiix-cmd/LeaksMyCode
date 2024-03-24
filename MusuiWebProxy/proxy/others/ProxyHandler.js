let instance;
class ProxyHandler
{
    players = new Map();

    constructor()
    {
    }

    getSession(name)
    {
        return this.players.get(name);
    }

    addSession(name, session)
    {
        this.players.set(name, session);
    }
}
module.exports = {
    getInstance()
    {
        return instance ? instance : (instance = new ProxyHandler());
    }
}