<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\session\network\handler;

use Closure;
use Zwuiix\Libs\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}