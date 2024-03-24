<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type;

use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use Zwuiix\Libs\muqsit\invmenu\inventory\InvMenuInventory;
use Zwuiix\Libs\muqsit\invmenu\InvMenu;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\BlockInvMenuGraphic;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use Zwuiix\Libs\muqsit\invmenu\type\util\InvMenuTypeHelper;

final class BlockFixedInvMenuType implements FixedInvMenuType{

	public function __construct(
		private Block $block,
		private int $size,
		private ?InvMenuGraphicNetworkTranslator $network_translator = null
	){}

	public function getSize() : int{
		return $this->size;
	}

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
		$origin = $player->getPosition()->addVector(InvMenuTypeHelper::getBehindPositionOffset($player))->floor();
		if(!InvMenuTypeHelper::isValidYCoordinate($origin->y)){
			return null;
		}

		return new BlockInvMenuGraphic($this->block, $origin, $this->network_translator);
	}

	public function createInventory() : Inventory{
		return new InvMenuInventory($this->size);
	}
}