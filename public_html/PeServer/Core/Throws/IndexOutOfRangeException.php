<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\CoreException;

class IndexOutOfRangeException extends CoreException
{
	use ThrowableTrait;
}
