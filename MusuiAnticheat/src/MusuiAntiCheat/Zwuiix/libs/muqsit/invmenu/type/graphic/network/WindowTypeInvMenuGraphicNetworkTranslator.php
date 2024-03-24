<?php

declare(strict_types=1);

namespace MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\type\graphic\network;

use MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\session\InvMenuInfo;
use MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}