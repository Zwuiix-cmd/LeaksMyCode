<?php

namespace PlutooCore\interface;

use MusuiEssentials\libs\jojoe77777\FormAPI\SimpleForm;
use MusuiEssentials\MusuiPlayer;

class EggUI
{
    /**
     * @param MusuiPlayer $musuiPlayer
     * @param string $text
     * @return void
     */
    public static function send(MusuiPlayer $musuiPlayer): void
    {
        $form = new SimpleForm(function (MusuiPlayer $player, mixed $data = null) {
            if(is_null($data)) return;
        });
        $form->setTitle("§m§z"); // §m§f => text stylé
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§r");
        $form->addButton("§rParcourir les factions§h§d", 0, "textures/items/egg");
        $form->sendToPlayer($musuiPlayer);
    }
}