const v8 = require('v8');
v8.setFlagsFromString('--max-old-space-size=4096');

const express = require("express");
const app = express();
const App = require("./back/App");
const Process = require("process");

function getUptime()
{
    let time = Math.round(process.uptime());

    let minutes = Math.round(Math.abs(time / 60));
    let seconds = Math.round(Math.abs(time - minutes * 60));

    let times = [];
    if(minutes !== 0) {
        times.push(`${minutes} minute(s)`);
    }
    if(seconds !== 0) {
        times.push(`${seconds} second(s)`);
    }

    return times.join(", ");
}

setInterval(() => {
    process.title = `NameMcbe / Discord: .gg/KhWnNWmgCs / UpTime: ${getUptime()} | PID: #${process.pid}`;
}, 1000)
process.setMaxListeners(Number.MAX_VALUE);
switch (process.platform) {
    case "win32":
    case "darwin":
    case "linux":
        break;
    default:
        console.error("Sorry, this OS is not compatible");
        return;
}

new App(app);