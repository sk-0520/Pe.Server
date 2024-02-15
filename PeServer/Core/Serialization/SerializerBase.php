<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use Throwable;
use PeServer\Core\Binary;
use PeServer\Core\Serialization\ISerializer;
use PeServer\Core\Throws\DeserializeException;
use PeServer\Core\Throws\SerializeException;
use PeServer\Core\Throws\Throws;

/**
 * シリアライザー基底処理。
 */
abstract class SerializerBase implements ISerializer
{
	#region function

	/**
	 * シリアライズ処理実装。
	 *
	 * @param array<mixed>|object $value
	 * @return Binary
	 * @throws Throwable 実装側で投げられた例外は全て `SerializeException` として扱われる。
	 */
	abstract protected function saveImpl(array|object $value): Binary;

	/**
	 * デシリアライズ処理実装。
	 *
	 * @param Binary $value
	 * @return array<mixed>|object
	 * @throws Throwable 実装側で投げられた例外は全て `DeserializeException` として扱われる。
	 */
	abstract protected function loadImpl(Binary $value): array|object;

	#endregion

	#region ISerializer

	public function save(array|object $value): Binary
	{
		try {
			return $this->saveImpl($value);
		} catch (Throwable $ex) {
			Throws::reThrow(SerializeException::class, $ex);
		}
	}

	public function load(Binary $value): array|object
	{
		try {
			return $this->loadImpl($value);
		} catch (Throwable $ex) {
			Throws::reThrow(DeserializeException::class, $ex);
		}
	}
	#endregion
}
