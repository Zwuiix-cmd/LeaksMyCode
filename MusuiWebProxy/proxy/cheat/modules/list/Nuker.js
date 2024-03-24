const Module = require('../Module');
const {Vector3} = require("../../../utils/math/Vector3");
const ItemBasic = require("../../item/BasicItem");
class Nuker extends Module
{
    constructor(session) {
        super(session, "Nuker", {
            minX: 0, maxX: 0,
            minY: 0, maxY: 0,
            minZ: 0, maxZ: 0,
        });
    }

    onUpdate(data)
    {
        this.flags.minX = parseInt(this.cleanValue(data.nuker_minx, 0));
        this.flags.maxX = parseInt(this.cleanValue(data.nuker_maxx, 0));
        this.flags.minY = parseInt(this.cleanValue(data.nuker_miny, 0));
        this.flags.maxY = parseInt(this.cleanValue(data.nuker_maxy, 0));
        this.flags.minZ = parseInt(this.cleanValue(data.nuker_minz, 0));
        this.flags.maxZ = parseInt(this.cleanValue(data.nuker_maxz, 0));
    }

    handlePacket(type, packet)
    {
        if(type !== "serverbound") {
            return;
        }

        if(packet.name !== "player_auth_input") {
            return;
        }

        if(packet.params.transaction !== undefined) {
            let data = packet.params.transaction.data;
            if(data.action_type === "break_block") {
                let blockPosition = data.block_position;

                let minX = blockPosition.x - this.flags.minX;
                let maxX = blockPosition.x + this.flags.maxX;
                let minY = blockPosition.y - this.flags.minY;
                let maxY = blockPosition.y + this.flags.maxY;
                let minZ = blockPosition.z - this.flags.minZ;
                let maxZ = blockPosition.z + this.flags.maxZ;

                let blockTotalCount = this.calculateBlockCount(minX, maxX, minY, maxY, minZ, maxZ);
                let stackBlockPosition = [];
                let breakInterval = undefined;

                if(blockTotalCount < 65) {
                    for (let x = minX; x <= maxX; x++) {
                        for (let y = minY; y <= maxY; y++) {
                            for (let z = minZ; z <= maxZ; z++) {
                                this.session.breakBlock(new Vector3(x, y, z));
                            }
                        }
                    }
                    return;
                }

                for (let x = minX; x <= maxX; x++) {
                    for (let y = minY; y <= maxY; y++) {
                        for (let z = minZ; z <= maxZ; z++) {
                            stackBlockPosition.push(new Vector3(x, y, z));
                        }
                    }
                }

                for (let x = maxX; x >= minX; x--) {
                    for (let y = maxY; y >= minY; y--) {
                        for (let z = maxZ; z >= minZ; z--) {
                            stackBlockPosition.push(new Vector3(x, y, z));
                        }
                    }
                }

                let total = 0;
                breakInterval = setInterval(() => {
                    if(total >= stackBlockPosition.length) {
                        clearInterval(breakInterval);
                        return;
                    }

                    let countBlock = 0;
                    stackBlockPosition.forEach((position, index) => {
                        if(countBlock >= 65) {
                            return;
                        }
                        delete(stackBlockPosition[index]);
                        this.session.move(position.add(0, 1.62, 0), 0, 0, "normal");
                        setTimeout(() => {
                            this.session.move(position.add(0, 1.62, 0), 0, 0, "teleport");
                            this.session.breakBlock(position);
                        }, 25);
                        countBlock++;
                        total++;
                    });
                }, 1000);
            }
        }
    }

    calculateBlockCount(
        minX, maxX,
        minY, maxY,
        minZ, maxZ
    )
    {
        let i = 0;
        for (let x = minX; x <= maxX; x++) {
            for (let y = minY; y <= maxY; y++) {
                for (let z = minZ; z <= maxZ; z++) {
                    i++;
                }
            }
        }

        return i;
    }
}
module.exports = Nuker;