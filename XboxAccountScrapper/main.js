const axl = require("app-xbox-live");

let token = undefined;
let xl = undefined;

async function initialize()
{
    token = await axl.Token("ymhqoddqrhub@outlook.com", ";mv>N1_MYT");
    xl = await new axl.Account(`XBL3.0 x=${token[1]};${token[0]}`);
}
initialize().then(r => {
    setInterval(async () => {
        searchUsername(generateRandomText(4));
    }, 2500);
});

function generateRandomText(length) {
    const letters = 'abcdefghijklmnopqrstuvwxyz';
    const digitsAndLetters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let randomText = '';
    randomText += letters.charAt(Math.floor(Math.random() * letters.length));
    for (let i = 1; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * digitsAndLetters.length);
        randomText += digitsAndLetters.charAt(randomIndex);
    }
    return randomText;
}

function searchUsername(name)
{
    xl.people.find(name, 1).then(e => {
        if(name.toLowerCase() !== e.people[0].gamertag.toLowerCase()) {
            console.log(name);
        }
    }).catch(err => {
        if(err.message === "Cannot read properties of undefined (reading 'gamertag')") {
            console.log(name);
            return;
        }
        console.log(`${name} => ${err.message}`);
    });
}

