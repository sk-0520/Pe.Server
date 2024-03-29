<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use PeServer\Core\Binary;
use PeServer\Core\Throws\SerializeException;
use PeServer\Core\Throws\DeserializeException;

interface ISerializer
{
	#region function

	/**
	 * シリアライズ処理。
	 *
	 * @param array<mixed>|object $value
	 * @return Binary
	 * @throws SerializeException
	 */
	public function save(array|object $value): Binary;

	/**
	 * デシリアライズ処理。
	 *
	 * @param Binary $value
	 * @return array<mixed>|object
	 * @throws DeserializeException
	 */
	public function load(Binary $value): array|object;

	#endregion
}
