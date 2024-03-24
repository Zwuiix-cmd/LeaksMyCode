<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type;

use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use Zwuiix\Libs\muqsit\invmenu\InvMenu;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}