<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\ValidationReceivable;

class Validator
{
	public const COMMON = '';

	public const KIND_EMPTY = 0;
	public const KIND_WHITE_SPACE = 1;
	public const KIND_LENGTH = 2;
	public const KIND_RANGE = 3;
	public const KIND_MATCH = 4;
	public const KIND_EMAIL = 5;
	public const KIND_WEBSITE = 6;

	/**
	 * 検証移譲取得処理。
	 *
	 * @var ValidationReceivable
	 */
	private $_receiver;

	public function __construct(ValidationReceivable $receiver)
	{
		$this->_receiver = $receiver;
	}

	public function isNotEmpty(string $key, ?string $value): bool
	{
		if (StringUtility::isNullOrEmpty($value)) {
			$this->_receiver->receiveErrorKind($key, self::KIND_EMPTY, ['value' => $value]);
			return false;
		}

		return true;
	}

	public function isNotWhiteSpace(string $key, ?string $value): bool
	{
		if (StringUtility::isNullOrWhiteSpace($value)) {
			$this->_receiver->receiveErrorKind($key, self::KIND_WHITE_SPACE, ['value' => $value]);
			return false;
		}

		return true;
	}


	public function inLength(string $key, int $length, string $value): bool
	{
		if ($length < mb_strlen($value)) {
			$this->_receiver->receiveErrorKind($key, self::KIND_LENGTH, ['value' => $value, 'safe-length' => $length, 'error-length' => mb_strlen($value)]);
			return false;
		}

		return true;
	}

	public function inRange(string $key, int $min, int $max, string $value): bool
	{
		$length = mb_strlen($value);
		if ($length < $min || $max < $length) {
			$this->_receiver->receiveErrorKind($key, self::KIND_RANGE, ['value' => $value, 'min' => $min, 'max' => $max, 'error-length' => mb_strlen($value)]);
			return false;
		}

		return true;
	}

	public function isMatch(string $key, string $pattern, string $value): bool
	{
		if (!preg_match($pattern, $value)) {
			$this->_receiver->receiveErrorKind($key, self::KIND_MATCH, ['value' => $value, 'pattern' => $pattern]);
			return false;
		}

		return true;
	}

	public function isEmail(string $key, string $value): bool
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return true;
		}

		$this->_receiver->receiveErrorKind($key, self::KIND_EMAIL, ['value' => $value]);

		return false;
	}

	public function isWebsite(string $key, string $value): bool
	{
		if (filter_var($value, FILTER_VALIDATE_URL)) {
			if (preg_match('|^https?://.+|', $value)) {
				return true;
			}
		}

		$this->_receiver->receiveErrorKind($key, self::KIND_WEBSITE, ['value' => $value]);

		return false;
	}
}