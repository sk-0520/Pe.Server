<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\DiContainerException;

class DiContainerUndefinedTypeException extends DiContainerException
{
	use ThrowableTrait;
}
