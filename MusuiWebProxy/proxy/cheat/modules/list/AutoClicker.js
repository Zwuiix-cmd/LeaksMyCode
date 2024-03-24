const Module = require('../Module');
const MathJS = require("../../../utils/MathJS");
class AutoClicker extends Module
{
    interval = undefined;
    skipClick = false;

    constructor(session) {
        super(session, "AutoClicker", {cps: 10, randomize: false, sprinting: false});
    }

    onUpdate(data)
    {
        this.flags.cps = this.cleanValue(data.autoclicker_cps_value, 10);
        this.flags.randomize = data.autoclicker_randomize === "on";
        this.flags.sprinting = data.autoclicker_sprinting === "on";

        if(this.status) {
            if(this.interval !== undefined) {
                clearInterval(this.interval);
            }
            this.interval = setInterval(() => {
                this.autoClicker();
            }, Math.round((100 / this.flags.cps)));
        } else {
            if(this.interval !== undefined) {
                clearInterval(this.interval);
                this.interval = undefined;
            }
        }
    }

    autoClicker()
    {
        if(this.flags.sprinting) {
            if(!this.session.isSprinting) {
                return;
            }
        }

        if(this.flags.randomize) {
            if(MathJS.rand(1, 3) === 3) {
                if(MathJS.rand(1, 6) === 6) this.skipClick = true;
                return;
            }
            if(this.skipClick) {
                if(MathJS.rand(1, 12) !== 12) this.skipClick = false;
                return;
            }
        }

        let distanceMax = 3;
        if(this.session.cursorEntity.entity !== undefined) {
            if(this.session.getPosition() && this.session.getPosition().distance(this.session.cursorEntity.position) <= distanceMax) {
                this.session.attack(this.session.cursorEntity.entity);
                return;
            }
        }

        let resp = this.session.canAttackOverEntity();
        if(resp !== null) {
            this.session.attack(resp);
            return;
        }

        this.session.swingArm();
    }
}
module.exports = AutoClicker;