<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Logic;

use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Mvc\Logic\IValidationReceiver;

/**
 * 共通検証処理。
 */
class Validator
{
	#region define

	public const COMMON = Text::EMPTY;

	public const KIND_EMPTY = 0;
	public const KIND_WHITE_SPACE = 1;
	public const KIND_LENGTH = 2;
	public const KIND_RANGE = 3;
	public const KIND_MATCH = 4;
	public const KIND_EMAIL = 5;
	public const KIND_WEBSITE = 6;

	#endregion

	#region variable

	/**
	 * 検証移譲取得処理。
	 */
	private IValidationReceiver $receiver;
	private Regex $regex;

	#endregion

	public function __construct(IValidationReceiver $receiver)
	{
		$this->regex = new Regex();
		$this->receiver = $receiver;
	}

	#region function

	public function isNotEmpty(string $key, ?string $value): bool
	{
		if (Text::isNullOrEmpty($value)) {
			$this->receiver->receiveErrorKind($key, self::KIND_EMPTY, ['VALUE' => Text::EMPTY]);
			return false;
		}

		return true;
	}

	/**
	 * ホワイトスペース以外か。
	 *
	 * @param string $key
	 * @param string|null $value
	 * @return bool
	 * @phpstan-assert-if-true non-empty-string $value
	 */
	public function isNotWhiteSpace(string $key, ?string $value): bool
	{
		if (Text::isNullOrWhiteSpace($value)) {
			$this->receiver->receiveErrorKind($key, self::KIND_WHITE_SPACE, ['VALUE' => $value ? $value : Text::EMPTY]);
			return false;
		}

		return true;
	}


	public function inLength(string $key, int $length, string $value): bool
	{
		if ($length < Text::getLength($value)) {
			$this->receiver->receiveErrorKind($key, self::KIND_LENGTH, ['VALUE' => $value, 'SAFE_LENGTH' => $length, 'ERROR_LENGTH' => mb_strlen($value)]);
			return false;
		}

		return true;
	}

	public function inRange(string $key, int $min, int $max, string $value): bool
	{
		$length = Text::getLength($value);
		if ($length < $min || $max < $length) {
			$this->receiver->receiveErrorKind($key, self::KIND_RANGE, ['VALUE' => $value, 'RANGE_MIN' => $min, 'RANGE_MAX' => $max, 'ERROR_LENGTH' => mb_strlen($value)]);
			return false;
		}

		return true;
	}

	/**
	 * 正規表現にマッチするか。
	 *
	 * @param string $key
	 * @param string $pattern
	 * @phpstan-param literal-string $pattern
	 * @param string $value
	 * @return bool
	 */
	public function isMatch(string $key, string $pattern, string $value): bool
	{
		if (!$this->regex->isMatch($value, $pattern)) {
			$this->receiver->receiveErrorKind($key, self::KIND_MATCH, ['VALUE' => $value, 'PATTERN' => $pattern]);
			return false;
		}

		return true;
	}

	/**
	 * 正規表現にマッチしないか。
	 *
	 * @param string $key
	 * @param string $pattern
	 * @phpstan-param literal-string $pattern
	 * @param string $value
	 * @return bool
	 */
	public function isNotMatch(string $key, string $pattern, string $value): bool
	{
		if ($this->regex->isMatch($value, $pattern)) {
			$this->receiver->receiveErrorKind($key, self::KIND_MATCH, ['value' => $value, 'pattern' => $pattern]);
			return false;
		}

		return true;
	}
	public function isEmail(string $key, string $value): bool
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return true;
		}

		$this->receiver->receiveErrorKind($key, self::KIND_EMAIL, ['VALUE' => $value]);

		return false;
	}

	public function isWebsite(string $key, string $value): bool
	{
		if (filter_var($value, FILTER_VALIDATE_URL)) {
			if (preg_match('|^https?://.+|', $value)) {
				return true;
			}
		}

		$this->receiver->receiveErrorKind($key, self::KIND_WEBSITE, ['VALUE' => $value]);

		return false;
	}

	#endregion
}
