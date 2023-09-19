<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\ArgumentException;

class ArgumentOutOfRangeException extends ArgumentException
{
	use ThrowableTrait;
}
