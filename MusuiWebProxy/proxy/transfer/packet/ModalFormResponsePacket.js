const FormID = require("../form/FormID");

class ModalFormResponsePacket
{
    constructor(packet)
    {
        /*** @type {Form}*/
        let form = FormID.getFormDataByID(packet.params.form_id);
        if (!form) return;
        let player_form = form.getPlayer();
        if(player_form !== player) return false;
        if(!packet.params.has_response_data) return false;
        form.handleCallable(packet.params.data);
    }
}
module.exports = ModalFormResponsePacket;