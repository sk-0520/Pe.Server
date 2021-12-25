<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\CoreInitializer;
use \PeServer\Core\InitializeChecker;
use \PeServer\App\Models\AppConfiguration;

abstract class SessionKey
{
	public const ACCOUNT = 'account';
}
