<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\AccessException;

class AccessKeyNotFoundException extends AccessException
{
	use ThrowableTrait;
}
