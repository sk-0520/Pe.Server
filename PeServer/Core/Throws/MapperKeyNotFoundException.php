<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\MapperException;

class MapperKeyNotFoundException extends MapperException
{
	use ThrowableTrait;
}
