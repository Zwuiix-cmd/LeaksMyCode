<?php

declare(strict_types=1);

namespace PracticeCore\Zwuiix\libs\muqsit\invmenu\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface InvMenuTransaction{

	public function getPlayer() : Player;

	public function getOut() : Item;

	public function getIn() : Item;

	/**
	 * Returns the item that was clicked / taken out of the inventory.
	 *
	 * @return Item
     * @link \PracticeCore\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransaction::getOut()
	 */
	public function getItemClicked() : Item;

	/**
	 * Returns the item that an item was clicked with / placed in the inventory.
	 *
	 * @return Item
     * @link \PracticeCore\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransaction::getIn()
	 */
	public function getItemClickedWith() : Item;

	public function getAction() : SlotChangeAction;

	public function getTransaction() : InventoryTransaction;

	public function continue() : InvMenuTransactionResult;

	public function discard() : InvMenuTransactionResult;
}