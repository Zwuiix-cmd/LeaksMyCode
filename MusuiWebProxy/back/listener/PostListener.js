const PlayersHandler = require("../../proxy/others/PlayersHandler");
const Server = require("../../proxy/transfer/Server");
const Proxy = require('../../proxy/cheat/Proxy');
const ProxyHandler = require("../../proxy/others/ProxyHandler");
const AccountsHandler = require("../../proxy/others/AccountsHandler");
const Path = require("path");
const e = require("express");
const Session = require("../../proxy/cheat/session/Session");
const EClient = require("../../proxy/transfer/client/EClient");
const express = require("express");
const bodyParser = require("body-parser");

class PostListener
{
    constructor(webApp, App)
    {
        webApp.listen(3000, "0.0.0.0", () => {
            console.log("Application started and Listening on http://localhost:3000");
        });

        webApp.use(express.static(Path.join(process.cwd() + "/web/")));
        webApp.use(bodyParser.urlencoded({ extended: true }))
        webApp.use(bodyParser.json());

        webApp.get("/", (req, res) => {
            if(!this.checkUp(req, res)) {
                res.sendFile(Path.join(process.cwd() + "/web/login.html"));
                return;
            }

            res.redirect("/dashboard");
        });

        webApp.get('/signup', (req, res) => {
            res.sendFile(Path.join(process.cwd() + "/web/signup.html"));
        });

        webApp.post("/ipn", (req, res) => {
            if(!this.checkUp(req, res)) {
                res.sendFile(Path.join(process.cwd() + "/web/login.html"));
                return;
            }

            if (req.body.payment_status === "Completed") {
                let user = res.app.get('user');
                user.account.buy = true;
                AccountsHandler.getInstance().config.setNested(`${user.token}.buy`, true);
                AccountsHandler.getInstance().config.save();
                console.log(`${user.username} has made a payment, transaction id: ${req.body.txn_id}`);

                res.status(200).send("IPN reçu avec succès.");
            } else {
                res.status(200).send("IPN reçu avec succès.");
            }
        });

        webApp.get('/users', (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }
            let user = res.app.get("user");
            if(!user.account.admin) {
                return;
            }

            res.json(this.formatUsers());
        });

        webApp.post('/update-admin', (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }

            let user = res.app.get("user");
            if(!user.account.admin) {
                return;
            }

            let body = req.body;
            let name = body.name;
            let type = body.type;
            let value = body.value;

            let token = AccountsHandler.getInstance().getAccountTokenWithUsername(name);
            let account = AccountsHandler.getInstance().getAccount(token);
            account[type] = value;
            AccountsHandler.getInstance().getConfig().save();
        });

        webApp.get('/admin', (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }
            let user = res.app.get("user");
            if(!user.account.admin) {
                return;
            }

            res.sendFile(Path.join(process.cwd() + "/web/admin.html"));
        });

        webApp.get('/modules', (req, res) => {
            res.json([
                {
                    title: 'AirJump',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'airjump_status' },
                    ],
                },
                {
                    title: 'AntiImmobile',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'antiimmobile_status' },
                    ],
                },
                {
                    title: 'AntiVoid',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'antivoid_status' },
                    ],
                },
                {
                    title: 'AutoClicker',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'autoclicker_status' },
                        { label: 'Click per second', type: 'range', name: 'autoclicker_cps_value', min: 1, max: 30, step: 1, value: 10, unit: 'cps' },
                        { label: 'Randomize', type: 'checkbox', name: 'autoclicker_randomize' },
                        { label: 'Sprinting Only', type: 'checkbox', name: 'autoclicker_sprinting' }
                    ],
                },
                {
                    title: 'AutoFarm',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'autofarm_status' },
                    ],
                },
                {
                    title: 'AutoSneak',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'autosneak_status' },
                    ],
                },
                {
                    title: 'AutoSprint',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'autosprint_status' },
                    ],
                },
                {
                    title: 'Bhop',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'bhop_status' },
                        { label: 'Speed', type: 'range', name: 'bhop_speed_value', min: 0.10, max: 3, step: 0.01, value: 0.10 }
                    ],
                },
                {
                    title: 'BigBitch',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'bigbitch_status' },
                        { label: 'Target', type: 'text', name: 'bigbitch_target', placeholder: 'Steve' },
                        { label: 'Focus', type: 'checkbox', name: 'bigbitch_focus' },
                        { label: 'Behind his head', type: 'checkbox', name: 'bigbitch_behindhishead' },
                        { label: 'Client derp', type: 'checkbox', name: 'bigbitch_client_derp' },
                        { label: 'Third person', type: 'checkbox', name: 'bigbitch_third_person' },
                        { label: 'Server crash on death', type: 'checkbox', name: 'bigbitch_crash_death' }
                    ],
                },
                {
                    title: 'ClickTP',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'clicktp_status' },
                    ],
                },
                {
                    title: 'DeathPosition',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'deathposition_status' },
                    ],
                },
                {
                    title: 'Derp',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'derp_status' },
                    ],
                },
                {
                    title: 'Disabler',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'disabler_status' },
                    ],
                },
                {
                    title: 'FastEat',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'fasteat_status' },
                    ],
                },
                {
                    title: 'Fly',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'fly_status' },
                        { label: 'Speed', type: 'range', name: 'fly_speed_value', min: 1, max: 8, step: 0.01, value: 1 }
                    ],
                },
                {
                    title: 'Franky',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'franky_status' },
                    ],
                },
                {
                    title: 'Freecam',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'freecam_status' },
                    ],
                },
                {
                    title: 'Fullbright',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'fullbright_status' },
                    ],
                },
                {
                    title: 'Glide',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'glide_status' },
                        { label: 'Speed', type: 'range', name: 'glide_speed_value', min: -2.0, max: 2, step: 0.01, value: -0.15 }
                    ],
                },
                {
                    title: 'Health Checker',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'healthchecker_status' },
                        { label: 'Percent', type: 'range', name: 'healthchecker_percent', min: 2, max: 100, step: 1, value: 100 }
                    ],
                },
                {
                    title: 'HighJump',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'highjump_status' },
                        { label: 'Jump Height', type: 'range', name: 'highjump_jumpheight_value', min: 0.50, max: 5, step: 0.01, value: 1 }
                    ],
                },
                {
                    title: 'HitBox',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'hitbox_status' },
                        { label: 'Width', type: 'range', name: 'hitboxwidth', min: 0.6, max: 5, step: 0.1, value: 0.6 },
                        { label: 'Height', type: 'range', name: 'hitboxheight', min: 1.8, max: 5, step: 0.1, value: 1.8 },
                    ],
                },
                {
                    title: 'InstantBreak',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'instantbreak_status' },
                    ],
                },
                {
                    title: 'Jetpack',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'jetpack_status' },
                        { label: 'Speed', type: 'range', name: 'jetpack_speed_value', min: 1, max: 8, step: 0.01, value: 1 }
                    ],
                },
                {
                    title: 'KillAura',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'killaura_status' },
                        { label: 'Range', type: 'range', name: 'killaura_range', min: 3, max: 8, step: 0.01, value: 3, unit: 'blocks' },
                        { label: 'Click per second', type: 'range', name: 'killaura_cps_value', min: 1, max: 20, step: 1, value: 10, unit: 'cps' },
                        { label: 'Single', type: 'checkbox', name: 'killaura_single' }
                    ],
                },
                {
                    title: 'Motion',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'motion_status' },
                        { label: 'Speed', type: 'range', name: 'motion_speed_value', min: 1, max: 8, step: 0.01, value: 1 },
                    ],
                },
                {
                    title: 'NoClip',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'noclip_status' },
                    ],
                },
                {
                    title: 'NoFall',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'nofall_status' },
                    ],
                },
                {
                    title: 'NoHurtCam',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'nohurtcam_status' },
                    ],
                },
                {
                    title: 'NoRender',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'norender_status' },
                    ],
                },
                {
                    title: 'NoSound',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'nosound_status' },
                    ],
                },
                {
                    title: 'Nuker',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'nuker_status' },

                        { label: 'MinX', type: 'range', name: 'nuker_minx', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },
                        { label: 'MaxX', type: 'range', name: 'nuker_maxx', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },

                        { label: 'MinY', type: 'range', name: 'nuker_miny', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },
                        { label: 'MaxY', type: 'range', name: 'nuker_maxy', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },

                        { label: 'MinZ', type: 'range', name: 'nuker_minz', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },
                        { label: 'MaxZ', type: 'range', name: 'nuker_maxz', min: 0, max: 8, step: 1, value: 1, unit: 'blocks' },
                    ],
                },
                {
                    title: 'PacketLogger',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'packetlogger_status' },
                    ],
                },
                {
                    title: 'PacketLoss',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'packetloss_status' },
                    ],
                },
                {
                    title: 'Reach',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'reach_status' },
                        { label: 'Range', type: 'range', name: 'reach_range', min: 3, max: 8, step: 0.01, value: 3, unit: 'blocks' },
                        { label: 'Sprinting Only', type: 'checkbox', name: 'reach_sprinting' },
                    ],
                },
                {
                    title: 'Spammer',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'spammer_status' },
                        { label: 'Message', type: 'text', name: 'spammer_message', placeholder: '' },
                    ],
                },
                {
                    title: 'Speed',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'speed_status' },
                        { label: 'Value', type: 'range', name: 'speed_value', min: 0.1, max: 3, step: 0.01, value: 0.1 },
                    ],
                },
                {
                    title: 'TimeChanger',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'timechanger_status' },
                        { label: 'Time', type: 'range', name: 'timechanger_value', min: 0, max: 24000, step: 1, value: 0, unit: '' },
                    ],
                },
                {
                    title: 'Timer',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'timer_status' },
                        { label: 'Value', type: 'range', name: 'timer_value', min: 1, max: 2, step: 0.01, value: 1 },
                    ],
                },
                {
                    title: 'Velocity',
                    options: [
                        { label: 'Enabled', type: 'checkbox', name: 'velocity_status' },
                        { label: 'Horizontal', type: 'range', name: 'kbhorizontal', min: 0, max: 100, step: 0.1, value: 98, unit: '%' },
                        { label: 'Vertical', type: 'range', name: 'kbvertical', min: 0, max: 100, step: 0.1, value: 98, unit: '%' },
                    ],
                },
            ]);
        });

        webApp.post('/signup', (req, res) => {
            let body = req.body;

            let username = body["username"];
            let email = body["email"];
            let password = body["password"];

            if(AccountsHandler.getInstance().existAccount(email)) {
                return;
            }

            AccountsHandler.getInstance().createAccount(username, email, password);
            res.sendFile(Path.join(process.cwd() + "/web/login.html"));
        });

        webApp.get('/login', (req, res) => {
            res.sendFile(Path.join(process.cwd() + "/web/login.html"));
        });

        webApp.post('/login', (req, res) => {
            let body = req.body;

            let email = body["email"];
            let password = body["password"];

            if(email !== undefined && password !== undefined) {
                if(!AccountsHandler.getInstance().existAccount(email) && !AccountsHandler.getInstance().existAccountWithUsername(email)) {
                    return;
                }

                if(AccountsHandler.getInstance().existAccountWithUsername(email)) {
                    email = AccountsHandler.getInstance().getEmailAccountWithUsername(email);
                }

                let token = AccountsHandler.getInstance().getAccountTokenWithEmail(email);
                if(!AccountsHandler.getInstance().canAccessAccount(token, email, password)) {
                    return;
                }

                let account = AccountsHandler.getInstance().getAccountWithToken(token);
                res.app.set('user', {
                    token: token,
                    account: account
                });

                if(account.buy) {
                    res.redirect('/dashboard');
                } else res.sendFile(Path.join(process.cwd() + "/web/buy.html"));
            }
        });

        webApp.post('/dashboard_transfer', async (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }

            res.sendFile(Path.join(process.cwd() + "/web/transfer.html"));
        });

        webApp.post('/transfer', (req, res) => {
            if (!this.checkUp(req, res)) {
                return;
            }

            let body = req.body;
            let address = body["address"];
            let port = body["port"];

            let user = res.app.get('user');
            let name = user.account.username;

            let callback = (player) => {
                let proxy = new Proxy();
                let sendDashboard = false;
                proxy.load(name, address, parseInt(port)).then(value => {
                    sendDashboard = value;
                    /*if(!value) {
                        serverPlayer.isTransfered = false;
                        serverPlayer.sendMessage("§cSorry, this server is offline!");
                        return;
                    }*/

                    player.transfer(proxy.getHost(), proxy.getPort());
                    if(player instanceof EClient) {
                        serverPlayer.isTransfered = true;
                        serverPlayer.isConnected = false;
                    }
                });
                res.sendFile(Path.join(process.cwd() + "/web/dashboard.html"));
            };

            if (!this.checkUpConnected(req, res, "server")) {
                if(!this.checkUpConnected(req, res, "proxy")) {
                    return;
                }

                let session = ProxyHandler.getInstance().getSession(name);
                if(session instanceof Session) {
                    (callback)(session);
                }
                return;
            }

            let serverPlayer = Server.getInstance().getClientByName(name);
            (callback)(serverPlayer);
        });

        webApp.get('/dashboard', (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }

            if(!this.checkUpConnected(req, res, "all")) {
                if(this.checkUpConnected(req, res, "server")) {
                    res.sendFile(Path.join(process.cwd() + "/web/transfer.html"));
                    return;
                }

                // TODO: CONNECTER SUR AUCUN SERVEUR IG
                return;
            }

            res.sendFile(Path.join(process.cwd() + "/web/dashboard.html"));
        });

        webApp.post("/synchronize", (req, res) => {
            if(!this.checkUp(req, res)) {
                return;
            }

            let user = res.app.get("user");
            let token = user.token;
            let info = user.account;

            let session = ProxyHandler.getInstance().getSession(info.username);
            if(session instanceof Session) {
                if(session.isConnected) {
                    session.moduleManager.syncModule(req.body);
                    return;
                }

                return;
            }
        });
    }

    checkUpConnected(req, res, type = "proxy")
    {
        let user = res.app.get("user");
        let token = user.token;
        let info = user.account;

        let username = info.username;
        let session = ProxyHandler.getInstance().getSession(username);
        let serverPlayer = Server.getInstance().getClientByName(info.username);

        switch (type) {
            case "all":
                return (
                    session !== undefined &&
                    serverPlayer !== undefined &&
                    !serverPlayer.isConnected && serverPlayer.isTransfered
                );
            case "proxy":
                return session !== undefined && !serverPlayer.isConnected && serverPlayer.isTransfered;
            case "server":
                return serverPlayer !== undefined && serverPlayer.isConnected;
            default:
                return false;
        }
    }

    checkUp(req, res)
    {
        if(res.app.get("user") === undefined) {
            res.sendFile(Path.join(process.cwd() + "/web/login.html"));
            return false;
        }

        let user = res.app.get("user");
        if(user.token === undefined || user.account === undefined) {
            res.sendFile(Path.join(process.cwd() + "/web/login.html"));
            return false;
        }

        let account = user.account;
        if(
            account === undefined ||
            account.username === undefined ||
            account.email === undefined ||
            account.password === undefined ||
            account.creationDate === undefined ||
            account.admin === undefined ||
            account.buy === undefined
        ) {
            res.sendFile(Path.join(process.cwd() + "/web/login.html"));
            return false;
        }

        return true;
    }

    formatUsers()
    {
        let users = [];
        AccountsHandler.getInstance().getConfig().getAll(true).forEach((value) => {
            let account = value[1];
            let passwordLength = `${account.password}`.length;
            users.push({
                title: account.username,
                options: [
                    { label: 'Email', type: 'text', value: account.email },
                    { label: 'Password', type: 'text', value: "*".repeat(passwordLength) },
                    { label: 'Admin', type: 'checkbox', name: 'admin', checked: account.admin },
                    { label: 'Buy', type: 'checkbox', name: 'buy', checked: account.buy },
                ],
            });
        });

        return users;
    }
}
module.exports = PostListener;