<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type\graphic\network;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use Zwuiix\Libs\muqsit\invmenu\session\InvMenuInfo;
use Zwuiix\Libs\muqsit\invmenu\session\PlayerSession;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}