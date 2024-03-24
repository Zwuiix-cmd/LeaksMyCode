<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type\util\builder;

use Zwuiix\Libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}