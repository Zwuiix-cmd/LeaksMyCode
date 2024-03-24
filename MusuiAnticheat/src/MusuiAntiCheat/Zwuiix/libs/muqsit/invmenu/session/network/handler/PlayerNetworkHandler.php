<?php

declare(strict_types=1);

namespace MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\session\network\handler;

use Closure;
use MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}