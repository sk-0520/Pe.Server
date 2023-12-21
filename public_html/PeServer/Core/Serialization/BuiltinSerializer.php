<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use JsonException;
use Exception;
use PeServer\Core\Binary;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Throws\JsonDecodeException;
use PeServer\Core\Throws\JsonEncodeException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\TypeUtility;

/**
 * PHP組み込みシリアライザー。
 */
final class BuiltinSerializer extends SerializerBase
{
	#region SerializerBase

	protected function saveImpl(array|object $value): Binary
	{
		$data = serialize($value);
		return new Binary($data);
	}

	protected function loadImpl(Binary $value): array|object
	{
		$data = unserialize($value->raw);
		if (is_array($data) || is_object($data)) {
			return $data;
		}

		throw new Exception(TypeUtility::getType($data));
	}

	#endregion
}
