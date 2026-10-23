<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use DateInterval;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\IAttributeValue;

class TimeValue implements IAttributeValue
{
	public readonly DateInterval $value;

	public function __construct(
		string|DateInterval  $value
	) {
		if (is_string($value)) {
			$this->value = new DateInterval($value);
		} else {
			$this->value = $value;
		}
	}

	#region IAttributeValue

	public function toValue(): string
	{
		return $this->value->format('H:i');
	}

	#endregion
}
