<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\ParseException;

/**
 * 型系。
 *
 * PHPの型変換がおれの予想をはるかに超えてくる。つらい。
 */
abstract class TypeUtility
{
	#region define

	private const INT_PATTERN = '/^\s*(\+|\-)?\d+\s*$/';

	public const TYPE_BOOLEAN = "bool";
	public const TYPE_INTEGER = "int";
	public const TYPE_DOUBLE = "float";
	public const TYPE_STRING = "string";
	public const TYPE_ARRAY = "array";
	public const TYPE_OBJECT = "object";
	public const TYPE_RESOURCE = "resource"; //BUGS: つかえん
	public const TYPE_RESOURCE_CLOSED = "resource (closed)";
	public const TYPE_NULL = "null";

	#endregion

	#region function

	/**
	 * 文字列を整数に変換。
	 *
	 * @param string $input 文字列。
	 * @return integer 変換後整数。
	 * @throws ParseException 変換できない文字列。
	 */
	public static function parseInteger(string $input): int
	{
		$regex = new Regex();
		if (!$regex->isMatch($input, self::INT_PATTERN)) {
			throw new ParseException($input);
		}

		return (int)Text::trim($input);
	}

	/**
	 * 文字列を整数に変換した結果を取得。
	 *
	 * @param string $input 文字列。
	 * @param integer|null $result 変換成功時の整数。
	 * @return boolean 変換成功状態。
	 */
	public static function tryParseInteger(string $input, ?int &$result): bool
	{
		$regex = new Regex();
		if (!$regex->isMatch($input, self::INT_PATTERN)) {
			return false;
		}

		$result = (int)Text::trim($input);
		return true;
	}

	public static function parseBoolean(mixed $input): bool
	{
		if (is_bool($input)) {
			return (bool)$input;
		}
		if (is_string($input)) {
			$s = Text::toLower(Text::trim((string)$input));
			$trues = ['true', 't', 'on', 'ok', '1'];
			return Arr::containsValue($trues, $s);
		}

		return boolval($input);
	}

	public static function toString(mixed $input): string
	{
		if (is_string($input)) {
			return $input;
		}

		return strval($input);
	}

	/**
	 * 値から型を返す。
	 *
	 * @param mixed $input
	 * @return string 型名
	 * @phpstan-return class-string|self::TYPE_*
	 * @see https://www.php.net/manual/get_debug_type.php
	 */
	public static function getType(mixed $input): string
	{
		return get_debug_type($input); //@phpstan-ignore-line 正直戻り値の制限いらんとは思っている
	}

	public static function isNullable(string $type): bool
	{
		// if($type[0] === '?') {
		// 	return true;
		// }
		return match ($type) {
			self::TYPE_BOOLEAN => false,
			self::TYPE_INTEGER => false,
			self::TYPE_DOUBLE => false,
			self::TYPE_STRING => true,
			self::TYPE_ARRAY => true,
			self::TYPE_OBJECT => true,
			self::TYPE_RESOURCE => true,
			self::TYPE_RESOURCE_CLOSED => true,
			self::TYPE_NULL => true,
			default => true,
		};
	}

	#endregion
}
