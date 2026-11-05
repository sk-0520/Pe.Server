<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use DateTimeInterface;

class DateOnlyValue extends DateTimeValueBase
{
	public function __construct(
		string|DateTimeInterface $value
	) {
		parent::__construct($value, 'Y-m-d');
	}
}
