module.exports = {
    TAG_FIRST_PERSON: 0,
    TAG_FREE: 1,
    TAG_THIRD_PERSON: 2,
    TAG_THIRD_PERSON_FRONT: 3,

    getPNXPresets()
    {
        return [
            {
                identifier: {
                    type: "string",
                    value: "minecraft:first_person"
                },
                inherit_from: {
                    type: "string",
                    value: ""
                }
            },
            {
                pos_y: {
                    type: "float",
                    value: 0
                },
                identifier: {
                    type: "string",
                    value: "minecraft:free"
                },
                pos_z: {
                    type: "float",
                    value: 0
                },
                pos_x: {
                    type: "float",
                    value: 0
                },
                inherit_from: {
                    type: "string",
                    value: ""
                },
                rot_x: {
                    type: "float",
                    value: 0
                },
                rot_y: {
                    type: "float",
                    value: 0
                }
            },
            {
                identifier: {
                    type: "string",
                    value: "minecraft:third_person"
                },
                inherit_from: {
                    type: "string",
                    value: ""
                }
            },
            {
                identifier: {
                    type: "string",
                    value: "minecraft:third_person_front"
                },
                inherit_from: {
                    type: "string",
                    value: ""
                }
            }
        ];
    }
}