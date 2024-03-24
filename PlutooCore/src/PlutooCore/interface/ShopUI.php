<?php

namespace PlutooCore\interface;

use MusuiEssentials\libs\jojoe77777\FormAPI\CustomForm;
use MusuiEssentials\MusuiPlayer;
use PlutooCore\handlers\ShopHandler;
use PlutooCore\player\CustomMusuiPlayer;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

class ShopUI
{
    /**
     * @param MusuiPlayer $musuiPlayer
     * @param int $type
     * @param string $item
     * @param int $count
     * @param int $price
     * @return void
     */
    public static function send(CustomMusuiPlayer $musuiPlayer, int $type, string $item, int $count, int $price): void
    {
        $item = StringToItemParser::getInstance()->parse($item);
        if(!$item instanceof Item || $item->isNull()) {
            $musuiPlayer->sendMessage("§cItem invalide!");
            return;
        }
        $form = new CustomForm(function (CustomMusuiPlayer $musuiPlayer, array $data = null) use($type, $count, $price, $item) {
            if(is_null($data)) return;
            $n = abs(floatval($data["count"]));
            $value = $price * $n;
            switch ($type) {
                case ShopHandler::TYPE_BUY:
                    if(!$musuiPlayer->hasMoney($value)) {
                        $musuiPlayer->sendMessage("§cVous n'avez pas assez d'argent!");
                        return;
                    }

                    $item->setCount($n);
                    if(!$musuiPlayer->getInventory()->canAddItem($item)) {
                        $musuiPlayer->sendMessage("§cVous n'avez pas assez d'espace dans votre inventaire!");
                        return;
                    }

                    $musuiPlayer->getInventory()->addItem($item);
                    $musuiPlayer->reduceMoney($value);
                    $musuiPlayer->sendMessage("§9Vous avez bien acheté §5x{$n} {$item->getVanillaName()}§9 pour §5{$value}$ §9!");
                    break;
                case ShopHandler::TYPE_SELL:
                    $item->setCount($n);
                    if(!$musuiPlayer->getInventory()->contains($item)) {
                        $musuiPlayer->sendMessage("§cVous n'avez pas assez d'items!");
                        return;
                    }

                    $musuiPlayer->getInventory()->removeItem($item);
                    $musuiPlayer->addMoney($value);
                    $musuiPlayer->sendMessage("§9Vous avez bien vendu §5x{$n} {$item->getVanillaName()}§9 pour §5{$value}$ §9!");
                    break;
            }
        });
        $form->setTitle("Shop");
        $count = $musuiPlayer->getAllCount($item);
        $form->addLabel("Vous voici dans l'interface du shop.\nVous allez donc " . ($type == ShopHandler::TYPE_BUY ? "acheté" : "vendre") . " l'item: §9{$item->getVanillaName()}§r\nPrix: §9{$price} §f/ §9x{$count}§r\nVous avez: §9{$musuiPlayer->getMoney()}$\nVotre inventaire contients: §9x{$count} {$item->getVanillaName()}");
        $form->addInput("Nombre", default: ($type == ShopHandler::TYPE_BUY ? null : "{$count}"), label: "count");
        $musuiPlayer->sendForm($form);
    }
}