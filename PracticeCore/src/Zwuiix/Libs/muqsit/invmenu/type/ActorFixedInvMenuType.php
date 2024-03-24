<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type;

use pocketmine\inventory\Inventory;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\player\Player;
use Zwuiix\Libs\muqsit\invmenu\inventory\InvMenuInventory;
use Zwuiix\Libs\muqsit\invmenu\InvMenu;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\ActorInvMenuGraphic;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;

final class ActorFixedInvMenuType implements FixedInvMenuType{

	/**
	 * @param string $actor_identifier
	 * @param int $actor_runtime_identifier
	 * @param array<int, MetadataProperty> $actor_metadata
	 * @param int $size
	 * @param InvMenuGraphicNetworkTranslator|null $network_translator
	 */
	public function __construct(
		private string $actor_identifier,
		private int $actor_runtime_identifier,
		private array $actor_metadata,
		private int $size,
		private ?InvMenuGraphicNetworkTranslator $network_translator = null
	){}

	public function getSize() : int{
		return $this->size;
	}

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
		return new ActorInvMenuGraphic($this->actor_identifier, $this->actor_runtime_identifier, $this->actor_metadata, $this->network_translator);
	}

	public function createInventory() : Inventory{
		return new InvMenuInventory($this->size);
	}
}