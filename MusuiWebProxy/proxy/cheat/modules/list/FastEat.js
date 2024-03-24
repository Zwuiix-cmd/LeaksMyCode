const Module = require('../Module');
const EffectMap = require("../../effect/EffectMap");
const EffectIds = require("../../effect/EffectIds");
const Item = require("../../item/Item");
class FastEat extends Module
{
    constructor(session) {
        super(session, "FastEat", {});
    }
}
module.exports = FastEat;