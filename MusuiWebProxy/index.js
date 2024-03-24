const v8 = require('v8');
v8.setFlagsFromString('--max-old-space-size=4096');

const express = require("express");
const app = express();
const App = require("./back/App");
const ProxyTransfer = require("./proxy/transfer/ProxyTransfer");
const Process = require("./proxy/utils/Process");

setInterval(() => {
    process.title = `Musui - Web Client / Discord: .gg/KhWnNWmgCs / UpTime: ${Process.getUptime()} | PID: #${process.pid}`;
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
new ProxyTransfer();