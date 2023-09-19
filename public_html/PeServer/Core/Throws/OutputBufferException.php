<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\CoreException;

class OutputBufferException extends CoreException
{
	use ThrowableTrait;
}
