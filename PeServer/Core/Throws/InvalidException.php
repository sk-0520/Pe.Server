<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\CoreError;

final class InvalidException extends CoreError
{
	use ThrowableTrait;
}
