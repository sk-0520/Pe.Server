<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\CoreException;

class StringException extends CoreException
{
	use ThrowableTrait;
}
