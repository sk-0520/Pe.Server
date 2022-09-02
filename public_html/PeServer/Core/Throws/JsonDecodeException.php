<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\JsonExceptionBase;

/** @deprecated JsonSerializer */
class JsonDecodeException extends JsonExceptionBase
{
	use ThrowableTrait;
}
