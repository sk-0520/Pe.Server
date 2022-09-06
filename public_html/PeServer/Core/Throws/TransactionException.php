<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;

class TransactionException extends DatabaseException
{
	use ThrowableTrait;
}
