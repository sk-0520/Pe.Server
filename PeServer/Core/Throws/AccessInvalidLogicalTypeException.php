<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\AccessValueTypeException;

class AccessInvalidLogicalTypeException extends AccessValueTypeException
{
	use ThrowableTrait;
}
