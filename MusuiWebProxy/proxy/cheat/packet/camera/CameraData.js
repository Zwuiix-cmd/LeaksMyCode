const CameraSet = require("./options/CameraSet");
const CameraEase = require("./options/flag/CameraEase");
const CameraPos = require("./options/flag/CameraPos");
const {settings} = require("express/lib/application");
const CameraRot = require("./options/flag/CameraRot");
const CameraPreset = require("./options/flag/CameraPreset");
const Presets = require("./Presets");

class CameraData
{
    data = {
        type: "compound",
        name: "",
        value: {}
    };

    constructor(options)
    {
        this.data.value = options.toObject();
    }

    toObject()
    {
        return this.data;
    }
}
module.exports = CameraData;

function aimbotingInPS5()
{
    /*let cameraSet = new CameraSet();
    cameraSet.add(new CameraEase(1, "linear"));
    cameraSet.add(new CameraPos(session.getPosition()));
    cameraSet.add(new CameraRot(target.getYaw(), target.getPitch()));
    cameraSet.add(new CameraPreset(Presets.TAG_FREE));
    session.sendCameraInstruction(new CameraData(cameraSet));
    Result: Smooth Aimbot for all platform :)

    PacketLogged from PowerNukkitX and explication https://www.youtube.com/watch?v=9GV4_8tK7hw
    */
}