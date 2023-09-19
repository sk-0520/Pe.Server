<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\ResourceException;

class ResourceInvalidException extends ResourceException
{
	use ThrowableTrait;
}
