let instance;
class PlayersHandler
{
    players = new Map();

    constructor()
    {
    }

    getPlayer(token)
    {
        return this.players.get(token);
    }

    addPlayer(token, name)
    {
        this.players.set(token, name);
    }
}
module.exports = {
    getInstance()
    {
        return instance ? instance : (instance = new PlayersHandler());
    }
}