<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\ImageException;

class GraphicsException extends ImageException
{
	use ThrowableTrait;
}
