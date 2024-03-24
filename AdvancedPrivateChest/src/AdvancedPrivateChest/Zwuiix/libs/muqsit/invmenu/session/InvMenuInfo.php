<?php

declare(strict_types=1);

namespace AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\session;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenu;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic,
		public ?string $graphic_name
	){}
}