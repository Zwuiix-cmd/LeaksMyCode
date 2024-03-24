<?php

declare(strict_types=1);

namespace AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\inventory\InvMenuInventory;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenu;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\graphic\BlockInvMenuGraphic;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\util\InvMenuTypeHelper;
use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

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