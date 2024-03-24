<?php

declare(strict_types=1);

namespace MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\type\util\builder;

use MusuiAntiCheat\Zwuiix\libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}