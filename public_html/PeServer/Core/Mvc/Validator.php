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
	public const KIND_MATCH = 3;

	/**
	 * 検証移譲取得処理。
	 *
	 * @var ValidationReceivable
	 */
	private $_callback;

	public function __construct(ValidationReceivable $callback)
	{
		$this->_callback = $callback;
	}

	public function isNotEmpty(string $key, ?string $value): bool
	{
		if (StringUtility::isNullOrEmpty($value)) {
			$this->_callback->receiveError($key, self::KIND_EMPTY, ['value' => $value]);
			return false;
		}

		return true;
	}

	public function isNotWhiteSpace(string $key, ?string $value): bool
	{
		if (StringUtility::isNullOrWhiteSpace($value)) {
			$this->_callback->receiveError($key, self::KIND_WHITE_SPACE, ['value' => $value]);
			return false;
		}

		return true;
	}


	public function inLength(string $key, int $length, string $value): bool
	{
		if ($length < mb_strlen($value)) {
			$this->_callback->receiveError($key, self::KIND_LENGTH, ['value' => $value, 'safe-length' => $length, 'error-length' => mb_strlen($value)]);
			return false;
		}

		return true;
	}

	public function isMatch(string $key, string $pattern, string $value): bool
	{
		if (!preg_match($pattern, $value)) {
			$this->_callback->receiveError($key, self::KIND_MATCH, ['value' => $value, 'pattern' => $pattern]);
			return false;
		}

		return true;
	}
}
