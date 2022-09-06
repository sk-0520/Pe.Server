<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use \JsonException;
use \Throwable;
use PeServer\Core\Binary;
use PeServer\Core\Throws\JsonDecodeException;
use PeServer\Core\Throws\JsonEncodeException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\SerializeException;
use PeServer\Core\Throws\Throws;

/**
 * シリアライザー基底処理。
 */
abstract class SerializerBase
{
	#region function

	/**
	 * シリアライズ処理実装。
	 *
	 * @param array<mixed>|object $value
	 * @return Binary
	 */
	protected abstract function saveImpl(array|object $value): Binary;

	/**
	 * シリアライズ処理。
	 *
	 * @param array<mixed>|object $value
	 * @return Binary
	 */
	public function save(array|object $value): Binary
	{
		try {
			return $this->saveImpl($value);
		} catch (Throwable $ex) {
			Throws::reThrow(SerializeException::class, $ex);
		}
	}

	/**
	 * デシリアライズ処理実装。
	 *
	 * @param Binary $value
	 * @return array<mixed>|object
	 */
	protected abstract function loadImpl(Binary $value): array|object;

	/**
	 * デシリアライズ処理。
	 *
	 * @param Binary $value
	 * @return array<mixed>|object
	 */
	public function load(Binary $value): array|object
	{
		try {
			return $this->loadImpl($value);
		} catch (Throwable $ex) {
			Throws::reThrow(SerializeException::class, $ex);
		}
	}

	#endregion
}
