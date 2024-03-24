class ModalFormRequestPacket
{
    player;
    form_id;
    data;

    constructor(player)
    {
        this.player=player;
    }

    /**
     * @param id {number}
     */
    setFormID(id)
    {
        this.form_id=id;
    }

    /**
     * @param data {string}
     */
    setData(data)
    {
        this.data=data;
    }

    getData()
    {
        return {name: "modal_form_request", params: {form_id: this.form_id, data: this.data}};
    }
}

module.exports = ModalFormRequestPacket;