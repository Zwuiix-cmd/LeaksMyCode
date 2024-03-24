<?php

declare(strict_types=1);

namespace AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\util\builder;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}