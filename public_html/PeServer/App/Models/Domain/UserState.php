<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

abstract class UserState
{
	public const ENABLED = 'enabled';
	public const DISABLED = 'disabled';
	public const LOCKED = 'locked';
}
