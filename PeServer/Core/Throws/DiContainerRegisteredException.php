<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\DiContainerException;

class DiContainerRegisteredException extends DiContainerException
{
	use ThrowableTrait;
}
