<?php

declare(strict_types=1);

namespace MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\transaction;

use Closure;
use pocketmine\player\Player;

final class InvMenuTransactionResult{

	/** @var (Closure(Player) : void)|null */
	public ?Closure $post_transaction_callback = null;

	public function __construct(
		readonly public bool $cancelled
	){}

	/**
	 * @return bool
	 *@deprecated Access {@see \PracticeCore\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransactionResult::$cancelled} directly
	 */
	public function isCancelled() : bool{
		return $this->cancelled;
	}

	/**
	 * Notify when we have escaped from the event stack trace and the
	 * client's network stack trace.
	 * Useful for sending forms and other stuff that cant be sent right
	 * after closing inventory.
	 *
	 * @param (Closure(Player) : void)|null $callback
	 * @return self
	 */
	public function then(?Closure $callback) : self{
		$this->post_transaction_callback = $callback;
		return $this;
	}

	/**
	 * @return (Closure(Player) : void)|null
     * @deprecated Access {@see \PracticeCore\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransactionResult::$post_transaction_callback} directly
	 */
	public function getPostTransactionCallback() : ?Closure{
		return $this->post_transaction_callback;
	}
}