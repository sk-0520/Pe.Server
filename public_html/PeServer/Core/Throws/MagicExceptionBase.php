<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\CoreException;

abstract class MagicExceptionBase extends CoreException
{
	use ThrowableTrait;
}
