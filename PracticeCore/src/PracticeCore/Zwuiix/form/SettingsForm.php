<?php

namespace PracticeCore\Zwuiix\form;

use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\jojoe77777\FormAPI\CustomForm;
use PracticeCore\Zwuiix\session\Session;

class SettingsForm
{
    use SingletonTrait;

    /**
     * @param Session $session
     * @return void
     */
    public function send(Session $session): void
    {
        $form = new CustomForm(function (Session $session, array $data = null) {
            if(is_null($data)) return;

            $session->getInfo()->setScoreboard(is_bool($data[1]) && $data[1]);
            $session->getInfo()->setCps(is_bool($data[2]) && $data[2]);
            $session->getInfo()->setPrivateMessages(is_bool($data[3]) && $data[3]);
            $session->getInfo()->setPrivateMessagesSounds(is_bool($data[4]) && $data[4]);
        });
        $form->setTitle(LanguageHandler::getInstance()->translate("settings_form_title"));
        $form->addLabel(LanguageHandler::getInstance()->translate("settings_form_label"));
        $form->addToggle("Scoreboard", $session->getInfo()->hasScoreboard());
        $form->addToggle("CPS", $session->getInfo()->hasCps());
        $form->addToggle("PrivateMessages", $session->getInfo()->hasPrivateMessages());
        $form->addToggle("PrivateMessages Sounds", $session->getInfo()->hasPrivateMessagesSounds());

        $session->sendForm($form);
    }
}