<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\MagicExceptionBase;

class MagicPropertyException extends MagicExceptionBase
{
	use ThrowableTrait;
}
