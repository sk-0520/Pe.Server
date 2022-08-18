<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

use PeServer\Core\Throws\CoreException;
use Throwable;

class CacheException extends CoreException
{
	public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
