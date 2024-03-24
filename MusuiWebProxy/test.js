const fs = require('fs');
const { parse } = require('prismarine-nbt');
const Path = require("path");

async function readBlockStatesFile(file) {
    const buffer = fs.readFileSync(file);
    const list = [];

    try {
        let offset = 0;
        while (offset < buffer.length) {
            const { value, cursor } = parse(buffer, offset);
            list.push(value);
            offset = cursor;
        }

        return list;
    } catch (error) {
        console.error('Erreur lors de la lecture des données NBT :', error);
        return [];
    }
}

// Exemple d'utilisation
const file = Path.join(process.cwd() + "/stockage/bedrockData/canonical_block_states.nbt");
readBlockStatesFile(file)
    .then(list => {
        console.log(list);
    })
    .catch(error => {
        console.error(error);
    });
