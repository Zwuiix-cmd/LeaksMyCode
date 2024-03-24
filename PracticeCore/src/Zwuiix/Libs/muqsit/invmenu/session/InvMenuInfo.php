<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\session;

use Zwuiix\Libs\muqsit\invmenu\InvMenu;
use Zwuiix\Libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic,
		public ?string $graphic_name
	){}
}