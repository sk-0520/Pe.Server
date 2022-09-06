<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use \Error;

class CoreError extends Error
{
	use ThrowableTrait;
}
