<?php

namespace PracticeCore\Zwuiix\form;

use pocketmine\network\PacketHandlingException;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\handler\FFAHandler;
use PracticeCore\Zwuiix\ffa\FFA;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\jojoe77777\FormAPI\SimpleForm;
use PracticeCore\Zwuiix\session\Session;

class FFAForm
{
    use SingletonTrait;

    public function send(Session $session): void
    {
        $form = new SimpleForm(function (Session $session, int $data = null) {
            if(is_null($data)) return;
            $ffa = FFAHandler::getInstance()->getFFAById($data);
            if(!$ffa instanceof FFA) {
                throw new PacketHandlingException("FFA not exist");
            }

            $ffa->addPlayer($session);
            $session->sendMessage(LanguageHandler::getInstance()->translate("teleport_ffa_success", [$ffa->getName()]));
        });
        $form->setTitle(LanguageHandler::getInstance()->translate("ffa_form_title"));
        $form->setContent(LanguageHandler::getInstance()->translate("ffa_form_content"));

        foreach (FFAHandler::getInstance()->getAll() as $FFA) {
            $form->addButton(LanguageHandler::getInstance()->translate("ffa_form_button", [$FFA->getName(), $FFA->getPlayers()]), SimpleForm::IMAGE_TYPE_PATH, $FFA->getItemTexture());
        }

        $session->sendForm($form);
    }
}