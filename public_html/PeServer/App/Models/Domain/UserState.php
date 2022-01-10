<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\CoreInitializer;
use PeServer\Core\InitializeChecker;
use PeServer\App\Models\AppConfiguration;

abstract class UserState
{
	public const ENABLED = 'enabled';
	public const DISABLED = 'disabled';
	public const LOCKED = 'locked';
}
