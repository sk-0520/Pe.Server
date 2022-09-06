<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\DiContainerException;

class DiContainerArgumentException extends DiContainerException
{
	use ThrowableTrait;
}
