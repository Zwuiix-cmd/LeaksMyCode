<?php

namespace MusuiScanner\Zwuiix\form;

use MusuiScanner\Zwuiix\task\ScannerTask;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use virion\jojoe77777\FormAPI\CustomForm;
use virion\jojoe77777\FormAPI\SimpleForm;

class ScannerForm
{
    /**
     * @param Player $player
     * @return CustomForm
     */
    public function getForm(Player $player): CustomForm
    {
        $form = new CustomForm(function (Player $player, array $data = null): void {
            if(is_null($data)) {
                return;
            }

            $map = $data["map"] ?? null;
            if(!is_bool($map)) {
                return;
            }

            $inventory = $data["inventory"] ?? null;
            if(!is_bool($inventory)) {
                return;
            }

            $enderchests = $data["enderchests"] ?? null;
            if(!is_bool($enderchests)) {
                return;
            }

            $delete = $data["delete"] ?? null;
            if(!is_bool($delete)) {
                return;
            }

            if(!$map && !$inventory && !$enderchests) {
                $player->sendMessage(TextFormat::RED . "Please select a search mode!");
                return;
            }

            $this->onConfirm($player, $map, $inventory, $enderchests, $item, $count, $delete);
        });
        $form->setTitle("Scanner");
        $form->addLabel("Welcome to the scanner interface.");

        $form->addToggle("Search in map", false, label: "map");
        $form->addToggle("Search in players inventory", false, label: "inventory");
        $form->addToggle("Search in players enderchests", false, label: "enderchests");

        $form->addToggle("Delete found items", label: "delete");

        return $form;
    }

    /**
     * @param Player $player
     * @param bool $map
     * @param bool $inventory
     * @param bool $enderchests
     * @param int $item
     * @param int $count
     * @param bool $delete
     * @return void
     */
    private function onConfirm(Player $player, bool $map, bool $inventory, bool $enderchests, int $item, int $count, bool $delete): void
    {
        $item = $player->getInventory()->getItem($item);
        $form = new SimpleForm(function (Player $player, int $data = null) use($map, $inventory, $enderchests, $item, $count, $delete) {
            if(is_null($data)) {
                return;
            }
            if(!is_int($data)) {
                return;
            }

            switch ($data) {
                case 0:
                    new ScannerTask($player->getWorld(), $map, $inventory, $enderchests, $player->getInventory()->getContents(), $count, $delete);
                    break;
                case 1:
                    $player->sendMessage(TextFormat::RED . "You have cancelled the item search.");
                    break;
            }
        });
        $form->setTitle("Scanner");
        $content = ["Confirm you have selected all these tags?"];

        if($map) {
            $content[] = TextFormat::GRAY . "Check that the item is present throughout the map." . TextFormat::RESET;
        }
        if($inventory) {
            $content[] = TextFormat::GRAY . "Check that the item is present in all player inventories." . TextFormat::RESET;
        }
        if($enderchests) {
            $content[] = TextFormat::GRAY . "Check that the item is present in every player's enderchest." . TextFormat::RESET;
        }
        if($delete) {
            $content[] = TextFormat::GRAY . "All items found will be deleted directly from the server." . TextFormat::RESET;
        }

        $form->addButton("Confirm");
        $form->addButton("Cancel");

        $player->sendForm($form);
    }
}