<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use \Exception;

abstract class CoreException extends Exception
{
	use ThrowableTrait;
}
