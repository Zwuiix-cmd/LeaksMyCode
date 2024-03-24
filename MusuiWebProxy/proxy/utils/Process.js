module.exports = {
    getUptime()
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
}