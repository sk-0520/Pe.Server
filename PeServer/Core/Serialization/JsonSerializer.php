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

/**
 * JSONシリアライザー。
 */
class JsonSerializer extends SerializerBase
{
	#region define

	public const SAVE_NONE = 0;
	public const SAVE_PRETTY = JSON_PRETTY_PRINT;
	public const SAVE_UNESCAPED_SLASHES = JSON_UNESCAPED_SLASHES;
	public const SAVE_UNESCAPED_UNICODE = JSON_UNESCAPED_UNICODE;
	public const SAVE_DEFAULT = self::SAVE_PRETTY | self::SAVE_UNESCAPED_UNICODE;

	public const LOAD_NONE = 0;
	public const LOAD_DEFAULT = self::LOAD_NONE;

	public const DEFAULT_DEPTH = 512;

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $saveOption
	 * @phpstan-param int-mask-of<self::SAVE_*> $saveOption
	 * @param int $loadOption
	 * @phpstan-param int-mask-of<self::LOAD_*> $loadOption
	 * @param int $depth
	 * @phpstan-param positive-int $depth
	 */
	public function __construct(
		protected int $saveOption = self::SAVE_DEFAULT,
		protected int $loadOption = self::LOAD_DEFAULT,
		protected int $depth = self::DEFAULT_DEPTH
	) {
		//NOP
	}

	#region SerializerBase

	protected function saveImpl(array|object $value): Binary
	{
		$json = json_encode($value, $this->saveOption | JSON_THROW_ON_ERROR, $this->depth);
		if ($json == false) {
			throw new Exception(json_last_error_msg(), json_last_error());
		}

		return new Binary($json);
	}

	//@phpstan-ignore return.unusedType (objectは返らないけどインターフェイス的にこうなる)
	protected function loadImpl(Binary $value): array|object
	{
		$value = json_decode($value->toString(), true, $this->depth, $this->loadOption | JSON_THROW_ON_ERROR);
		if ($value === null) {
			throw new Exception(json_last_error_msg(), json_last_error());
		}

		return $value;
	}

	#endregion
}
