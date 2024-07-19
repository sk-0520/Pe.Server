<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization\Converter;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Utc;
use ReflectionNamedType;


/**
 * @extends TypeConverterBase<DateTimeImmutable|DateTimeInterface|null>
 */
class DateTimeConverter extends TypeConverterBase
{
	#region TypeConverterBase

	public function read(string $name, ReflectionNamedType $type, mixed $raw): DateTimeImmutable|DateTimeInterface|null
	{
		$targetClassName = $type->getName();
		switch ($targetClassName) {
			case DateTimeImmutable::class:
				if (is_string($raw) && !Text::isNullOrWhiteSpace($raw) && Utc::tryParse($raw, $result)) {
					return $result;
				}
				break;

			case DateTime::class:
			case DateTimeInterface::class:
				if (is_string($raw) && !Text::isNullOrWhiteSpace($raw) && Utc::tryParseDateTime($raw, $result)) {
					return $result;
				}
				break;

			default:
				break;
		}

		if ($type->allowsNull()) {
			return null;
		}

		throw new InvalidOperationException("$name: $targetClassName");
	}

	#endregion
}
