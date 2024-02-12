<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\MapperException;

class MapperTypeException extends MapperException
{
	use ThrowableTrait;
}
