<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use DateTimeInterface;
use DateTimeImmutable;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;

class DateTimeValueBase implements IAttributeValue
{
	public readonly DateTimeInterface $value;

	public function __construct(
		string|DateTimeInterface $value,
		protected string $format
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
		return $this->value->format($this->format);
	}

	#endregion
}
