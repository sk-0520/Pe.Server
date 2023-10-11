<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\CoreException;

class BinaryException extends CoreException
{
	use ThrowableTrait;
}
