<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Throws\RegexException;

class RegexPatternException extends RegexException
{
	use ThrowableTrait;
}
