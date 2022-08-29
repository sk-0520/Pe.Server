<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use \JsonException;
use PeServer\Core\Throws\JsonDecodeException;
use PeServer\Core\Throws\JsonEncodeException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\Throws;

/**
 * JSON処理系。
 */
class Json
{
	#region define

	public const ENCODE_OPTION_PRETTY = JSON_PRETTY_PRINT;
	public const ENCODE_OPTION_UNESCAPED_UNICODE = JSON_UNESCAPED_UNICODE;

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $depth
	 * @phpstan-param positive-int $depth
	 */
	public function __construct(
		protected int $depth = 512
	) {
	}

	#region function

	/**
	 * PHPデータをJSON文字列に変換。
	 *
	 * @param mixed $value
	 * @param int $options
	 * @phpstan-param int-mask-of<self::ENCODE_OPTION_*> $options
	 * @return string
	 * @throws JsonEncodeException
	 */
	public function encode(mixed $value, int $options = self::ENCODE_OPTION_PRETTY | self::ENCODE_OPTION_UNESCAPED_UNICODE): string
	{
		try {
			$json = json_encode($value, $options | JSON_THROW_ON_ERROR, $this->depth);
		} catch (JsonException $ex) {
			Throws::reThrow(JsonEncodeException::class, $ex, json_last_error_msg());
		}
		if ($json == false) {
			throw new ParseException(json_last_error_msg(), json_last_error());
		}

		return $json;
	}

	/**
	 * JSON文字列をPHP配列に変換。
	 *
	 * @param string $json
	 * @param int $options
	 * @return array<mixed>
	 * @throws JsonDecodeException
	 */
	public function decode(string $json, int $options = 0): array
	{
		try {
			$value = json_decode($json, true, $this->depth, $options | JSON_THROW_ON_ERROR);
		} catch (JsonException $ex) {
			Throws::reThrow(JsonDecodeException::class, $ex, json_last_error_msg());
		}

		if (is_null($value)) {
			throw new JsonDecodeException(json_last_error_msg(), json_last_error());
		}

		return $value;
	}

	#endregion
}
