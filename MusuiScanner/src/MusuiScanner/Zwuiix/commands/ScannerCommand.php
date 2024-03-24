<?php

namespace MusuiScanner\Zwuiix\commands;

use MusuiScanner\Zwuiix\form\ScannerForm;
use MusuiScanner\Zwuiix\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use virion\CortexPE\Commando\BaseCommand;

class ScannerCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(Plugin::getInstance()->getLoader(), "scanner", "Scan item in the world or inventory / enderchest", ["musuiscanner"]);
    }

    protected function prepare(): void
    {
        $this->setPermission("musuiscanner.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if($sender instanceof Player) {
            $form = new ScannerForm();
            $sender->sendForm($form->getForm($sender));
            return;
        }
        $sender->sendMessage(TextFormat::RED . "Please use this command in game.");
    }

    /**
     * @return array
     */
    public function getPermission(): array
    {
        return [];
    }
}