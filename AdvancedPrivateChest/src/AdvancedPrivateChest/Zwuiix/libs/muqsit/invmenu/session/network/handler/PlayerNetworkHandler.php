<?php

declare(strict_types=1);

namespace AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\session\network\handler;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\session\network\NetworkStackLatencyEntry;
use Closure;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}