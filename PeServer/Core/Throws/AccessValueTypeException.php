<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\AccessException;

class AccessValueTypeException extends AccessException
{
	use ThrowableTrait;
}
