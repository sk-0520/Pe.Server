<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\SerializeException;

class MapperException extends SerializeException
{
	use ThrowableTrait;
}
