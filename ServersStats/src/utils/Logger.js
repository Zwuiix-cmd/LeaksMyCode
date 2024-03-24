class Logger
{
    static error(content)
    {
        console.log(`[error] ${content}`);
    }

    static info(content)
    {
        console.log(`[info] ${content}`);
    }

    static debug(content)
    {
        console.log(`[debug] ${content}`);
    }
}
module.exports = Logger;
