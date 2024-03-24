const Module = require('../Module');
const EffectMap = require("../../effect/EffectMap");
const EffectIds = require("../../effect/EffectIds");
class Fullbright extends Module
{
    constructor(session) {
        super(session, "Fullbright", {});
    }

    onUpdate(data)
    {
        if(this.status) {
            this.session.sendEffect(EffectMap.ADD, EffectIds.NIGHT_VISION, 999999999, 0);
        } else this.session.sendEffect(EffectMap.REMOVE, EffectIds.NIGHT_VISION);
    }
}
module.exports = Fullbright;