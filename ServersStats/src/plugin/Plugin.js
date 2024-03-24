class Plugin
{
    name;

    constructor(name)
    {
        this.name = name;
    }

    getName()
    {
        return this.name;
    }
}
module.exports = Plugin;
