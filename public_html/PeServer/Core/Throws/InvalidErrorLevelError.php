<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\CoreError;

final class InvalidErrorLevelError extends CoreError
{
	use ThrowableTrait;
}
