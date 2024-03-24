const PostListener = require("./listener/PostListener");
const Path = require("path");

class App
{
    TAG_POST_LISTENER = "postlistener";

    webApp;
    listener = new Map();

    constructor(webApp)
    {
        this.webApp = webApp;
        this.listener.set(this.TAG_POST_LISTENER, new PostListener(webApp, this));
    }
}
module.exports = App;