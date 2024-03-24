const Path = require("path");
const {Config} = require("../utils/Config");
const e = require("express");
let instance;
class AccountsHandler
{
    config = new Config(Path.join(process.cwd() + "/stockage/accounts.json"));
    players = new Map();

    constructor()
    {
    }

    existAccount(email)
    {
        let exist= false;
        this.config.getAll(true).forEach((value) => {
            let token = value[0];
            let info = value[1];
            if(`${info.email}`.toLowerCase() === `${email}`.toLowerCase()) {
                exist = true;
                return exist;
            }
        });
        return exist;
    }

    existAccountWithUsername(username)
    {
        let exist= false;
        this.config.getAll(true).forEach((value) => {
            let token = value[0];
            let info = value[1];
            if(`${info.username}`.toLowerCase() === `${username}`.toLowerCase()) {
                exist = true;
                return exist;
            }
        });
        return exist;
    }

    getAccountWithToken(token)
    {
        return this.config.get(token, {});
    }

    getAccountTokenWithEmail(email)
    {
        let tokenFind= "none";
        this.config.getAll(true).forEach((value) => {
            let token = value[0];
            let info = value[1];
            if(`${info.email}`.toLowerCase() === `${email}`.toLowerCase()) {
                tokenFind = token;
                return tokenFind;
            }
        });
        return tokenFind;
    }

    getAccountTokenWithUsername(username)
    {
        let tokenFind= "none";
        this.config.getAll(true).forEach((value) => {
            let token = value[0];
            let info = value[1];
            if(`${info.username}`.toLowerCase() === `${username}`.toLowerCase()) {
                tokenFind = token;
                return tokenFind;
            }
        });
        return tokenFind;
    }

    getAccount(token)
    {
        return this.config.get(token, {});
    }

    getEmailAccountWithUsername(username)
    {
        let emailFind= "none";
        this.config.getAll(true).forEach((value) => {
            let info = value[1];
            if(`${info.username}`.toLowerCase() === `${username}`.toLowerCase()) {
                emailFind = info.email;
                return emailFind;
            }
        });
        return emailFind;
    }

    createAccount(username, email, password)
    {
        let token = this.generateToken(username, email, password);
        if(this.config.has(token)) return;
        this.config.set(token, {
            username: username,
            email: email,
            password: password,
            creationDate: new Date(Date.now()).toUTCString(),
            admin: false,
            buy: false,
        });
        this.config.save();
    }

    canAccessAccount(token, email, password)
    {
        let info = this.config.get(`${token}`, {});
        return info.email === email && info.password === password;
    }

    canUseWithUsername(username)
    {
        if(!this.existAccountWithUsername(username)) return false;
        let token = this.getAccountTokenWithUsername(username);
        let account = this.getAccount(token);
        return account.buy;
    }

    generateToken(username, email, birthday, password) {
        let token = [];
        token.push(btoa((btoa(btoa(username)))));
        token.push(btoa((btoa(btoa(email)))));
        token.push(btoa((btoa(btoa(birthday)))));
        token.push(btoa((btoa(btoa(password)))));

        return btoa(btoa(token.join(".")));
    }

    decodeToken(token)
    {
        let firstDecode = atob(token);
        let secondDecode = atob(firstDecode);

        let decode = [];
        let split = secondDecode.split(".");
        split.forEach((value) => {
           let first = atob(value);
           let second = atob(first);
           let three = atob(second);
           let four = atob(three);
           decode.push(four);
        });

        return decode;
    }

    getConfig()
    {
        return this.config;
    }
}
module.exports = {
    getInstance()
    {
        return instance ? instance : (instance = new AccountsHandler());
    }
}