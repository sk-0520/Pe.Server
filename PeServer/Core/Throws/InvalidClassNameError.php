<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\CoreError;

final class InvalidClassNameError extends CoreError
{
	use ThrowableTrait;
}
