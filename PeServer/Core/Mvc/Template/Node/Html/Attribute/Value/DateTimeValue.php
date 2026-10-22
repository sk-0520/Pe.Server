<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use DateTimeInterface;
use DateTimeImmutable;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;

class DateTimeValue implements IAttributeValue
{
	public readonly DateTimeInterface $value;

	public function __construct(
		string|DateTimeInterface $value
	) {
		if (is_string($value)) {
			$this->value = new DateTimeImmutable($value);
		} else {
			$this->value = $value;
		}
	}

	#region IAttributeValue

	public function toValue(): string
	{
		return $this->value->format(DateTimeInterface::ISO8601_EXPANDED);
	}

	#endregion
}
