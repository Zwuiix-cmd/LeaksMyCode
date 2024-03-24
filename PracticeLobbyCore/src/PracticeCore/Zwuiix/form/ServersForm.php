<?php

namespace PracticeCore\Zwuiix\form;

use pocketmine\network\PacketHandlingException;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\handler\ServersHandler;
use PracticeCore\Zwuiix\libs\jojoe77777\FormAPI\SimpleForm;
use PracticeCore\Zwuiix\server\Server;
use PracticeCore\Zwuiix\session\Session;

class ServersForm
{
    use SingletonTrait;

    public function send(Session $session): void
    {
        $form = new SimpleForm(function (Session $session, int $data = null) {
            if(is_null($data)) {
                $this->send($session);
                return;
            }
            $server = ServersHandler::getInstance()->getServerById($data);
            if(!$server instanceof Server) {
                throw new PacketHandlingException("Server not exist");
            }

            $server->transfer($session);
        });
        $form->setTitle(LanguageHandler::getInstance()->translate("servers_form_title"));
        $form->setContent(LanguageHandler::getInstance()->translate("servers_form_content"));

        foreach (ServersHandler::getInstance()->getAll() as $server) {
            $form->addButton(LanguageHandler::getInstance()->translate("server_form_button", [$server->getName(), $server->getPlayers()]));
        }

        $session->sendForm($form);
    }
}