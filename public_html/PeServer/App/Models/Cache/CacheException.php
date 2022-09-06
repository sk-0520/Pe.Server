<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

use PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\ThrowableTrait;
use Throwable;

class CacheException extends CoreException
{
	use ThrowableTrait;
}
