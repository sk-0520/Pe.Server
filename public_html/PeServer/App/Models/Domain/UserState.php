<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\Text;

abstract class UserState
{
	public const UNKNOWN = Text::EMPTY;
	public const ENABLED = 'enabled';
	public const DISABLED = 'disabled';
	public const LOCKED = 'locked';
}
