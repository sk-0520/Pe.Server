<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;

class SqlException extends DatabaseException
{
	use ThrowableTrait;
}
