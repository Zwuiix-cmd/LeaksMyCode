<?php

declare(strict_types=1);

namespace AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\inventory;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;

final class SharedInvMenuSynchronizer{

	private Inventory $inventory;
	private SharedInventorySynchronizer $synchronizer;
	private SharedInventoryNotifier $notifier;

	public function __construct(InvMenu $menu, Inventory $inventory){
		$this->inventory = $inventory;

		$menu_inventory = $menu->getInventory();
		$this->synchronizer = new SharedInventorySynchronizer($menu_inventory);
		$inventory->getListeners()->add($this->synchronizer);

		$this->notifier = new SharedInventoryNotifier($this->inventory, $this->synchronizer);
		$menu_inventory->setContents($inventory->getContents());
		$menu_inventory->getListeners()->add($this->notifier);
	}

	public function destroy() : void{
		$this->synchronizer->getSynchronizingInventory()->getListeners()->remove($this->notifier);
		$this->inventory->getListeners()->remove($this->synchronizer);
	}
}