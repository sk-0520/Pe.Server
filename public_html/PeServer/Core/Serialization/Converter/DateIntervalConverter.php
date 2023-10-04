<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization\Converter;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Error;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Time;
use ReflectionNamedType;


/**
 * @extends TypeConverterBase<DateInterval|null>
 */
class DateIntervalConverter extends TypeConverterBase
{
	#region TypeConverterBase

	public function read(string $name, ReflectionNamedType $type, mixed $raw): DateInterval|DateTimeInterface|null
	{
		$targetClassName = $type->getName();

		if (is_string($raw) && !Text::isNullOrWhiteSpace($raw)) {
			return Time::create($raw);
		}

		if ($type->allowsNull()) {
			return null;
		}

		throw new InvalidOperationException("$name: $targetClassName");
	}

	#endregion
}
