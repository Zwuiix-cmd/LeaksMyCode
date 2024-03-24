const EffectMap = require("../effect/EffectMap");
const EffectIds = require("../effect/EffectIds");

class Module
{
    session;
    name;
    flags;
    status = false;

    constructor(
        session,
        name,
        flags
    ) {
        this.session = session;
        this.name = name;
        this.flags = flags;
    }

    getName()
    {
        return this.name;
    }

    isEnabled()
    {
        return this.status;
    }

    setEnabled(bool)
    {
        this.status = bool;
    }

    cleanValue(value, def)
    {
        return (value === undefined || value === null) ? def : value;
    }

    syncOutboundPacket(name, packetData)
    {
        return packetData;
    }

    onUpdate(data) {}
    onTick() {};
    handlePacket(type, packet) {}
}
module.exports = Module;