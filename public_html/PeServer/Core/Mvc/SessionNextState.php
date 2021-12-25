<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

class SessionNextState
{
	public const NORMAL = 0;
	public const CANCEL = 1;
	public const RESTART = 2;
	public const SHUTDOWN = 3;
}
