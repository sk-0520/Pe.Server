<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization\Converter;

use ReflectionNamedType;

/**
 * シリアライズ型変換処理。
 *
 * @template TValue
 */
abstract class TypeConverterBase
{
	#region function

	/**
	 * 生値から型変換。
	 *
	 * @param string $name
	 * @param ReflectionNamedType $type
	 * @param mixed $raw
	 * @return mixed
	 * @phpstan-return TValue
	 */
	abstract public function read(string $name, ReflectionNamedType $type, mixed $raw): mixed;

	#endregion
}
