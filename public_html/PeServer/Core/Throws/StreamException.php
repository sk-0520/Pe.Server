<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\IOException;

class StreamException extends IOException
{
	use ThrowableTrait;
}
