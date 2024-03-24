const ps = require('prompt-sync');
const fsExtra = require('fs-extra');
const Proxy = require("./relay/Proxy");
const Path = require("path");
const prompt = ps(undefined);
const os = require('os');
const axios = require("axios");
const {CURRENT_VERSION} = require('../.././node_modules/bedrock-protocol/src/options');
const fs = require("fs");
const path = require("path");
const windowsShortcuts = require('windows-shortcuts');
const gradient = require("gradient-string");

class App
{
    constructor() {
        this.onEnable();
    }

    /**
     * Enabling System
     */
    onEnable()
    {
        let address = prompt("Address: ");
        if(address === "null") return;
        log(`Address: ${address}`);

        let port = prompt("Port: ");
        if(port === "null") return;
        log(`Port: ${port}`);

        log(` `);
        log(`Command: slq?nuker`);
        let radius = prompt("Radius: ");
        if(radius === "null") return;
        log(`Radius: ${radius}`);

        let bypass = prompt("Bypass (Y/N): ");
        if(bypass === "null") return;
        log(`Bypass: ${bypass.toLowerCase() === "y" ? "Oui" : "Non"}`);
        this.onStart(`${address}`, parseInt(port), parseInt(radius), bypass.toLowerCase() === "y");
    }

    /**
     * Starting bedrock Proxy
     * @param host {string}
     * @param port {number}
     * @param radius {number}
     * @param bypass {boolean}
     */
    onStart(host, port, radius, bypass)
    {
        new Proxy(host, port, radius, bypass);
    }
}

const lastData = [];
function log(data = null)
{
    console.clear();
    if(data !== null) lastData.push(data);
    let content = "" +
        "  ╔═════════════════════════════════════════════════════════════════════════════════╗  \n" +
        "  ║                                                                                 ║  \n" +
        "  ║  ███████╗██████╗ ███████╗███████╗  ███╗   ██╗██╗   ██╗██╗  ██╗███████╗██████╗   ║  \n" +
        "  ║  ██╔════╝██╔══██╗██╔════╝██╔════╝  ████╗  ██║██║   ██║██║ ██╔╝██╔════╝██╔══██╗  ║  \n" +
        "  ║  █████╗  ██████╔╝█████╗  █████╗    ██╔██╗ ██║██║   ██║█████╔╝ █████╗  ██████╔╝  ║  \n" +
        "  ║  ██╔══╝  ██╔══██╗██╔══╝  ██╔══╝    ██║╚██╗██║██║   ██║██╔═██╗ ██╔══╝  ██╔══██╗  ║  \n" +
        "  ║  ██║     ██║  ██║███████╗███████╗  ██║ ╚████║╚██████╔╝██║  ██╗███████╗██║  ██║  ║  \n" +
        "  ║  ╚═╝     ╚═╝  ╚═╝╚══════╝╚══════╝  ╚═╝  ╚═══╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝  ║  \n" +
        "  ║                                                                                 ║  \n" +
        "  ║  Made by @slq                                                                   ║  \n" +
        "  ║                                                                                 ║  \n";

    lastData.forEach((msg) => {
        let lenMax = 79;
        let voidmsg = Math.abs(lenMax - msg.length);
        content += "  ║  " + msg + " ".repeat(voidmsg) + "║  \n";
    });

    content += "  ║                                                                                 ║  \n" +
        "  ╚═════════════════════════════════════════════════════════════════════════════════╝  \n" +
        "\n";
    console.log(gradient('#00ffff', '#02a486')(content));
}

module.exports = log;

start();
async function start() {
    log();
    if (/*process.argv.includes("-s")*/true) {
        const response = await axios.get("https://raw.githubusercontent.com/PrismarineJS/bedrock-protocol/master/src/options.js");
        let split = `${response.data}`.split(/\r?\n/);
        if(split[5] !== undefined && split[5].includes("const CURRENT_VERSION = '")) {
            let findVersion = split[5].replaceAll("const CURRENT_VERSION = ", "").replaceAll("'", "");
            if(`${findVersion}` !== `${CURRENT_VERSION}`) {
                log("Your dependence is outdated, please update with « npm update »");
                log(`Path: ${Path.join(process.cwd())}`)
                return;
            }
        }

        new App();
    }
}