<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\CoreException;

/** @deprecated */
abstract class JsonExceptionBase extends CoreException
{
	use ThrowableTrait;
}
