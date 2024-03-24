<?php

declare(strict_types=1);

namespace PracticeCore\Zwuiix\libs\muqsit\invmenu\session;

use PracticeCore\Zwuiix\libs\muqsit\invmenu\InvMenu;
use PracticeCore\Zwuiix\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}