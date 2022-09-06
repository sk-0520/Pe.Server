<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\CoreException;

class ArchiveException extends CoreException
{
	use ThrowableTrait;
}
