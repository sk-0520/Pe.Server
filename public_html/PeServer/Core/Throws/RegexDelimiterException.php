<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;
use PeServer\Core\Throws\RegexException;

class RegexDelimiterException extends RegexException
{
	use ThrowableTrait;
}
