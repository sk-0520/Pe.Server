<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\MagicExceptionBase;

class MagicFunctionException extends MagicExceptionBase
{
	use ThrowableTrait;
}
