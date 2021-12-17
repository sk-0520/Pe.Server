<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\Mvc\IValidationReceiver;

class Validations
{
	public const KEY = 'key';

	public const KIND_LENGTH = 0;

	/**
	 * 検証移譲取得処理。
	 *
	 * @var IValidationReceiver
	 */
	private $callback;

	public function __construct(IValidationReceiver $callback)
	{
		$this->callback = $callback;
	}

	public function inLength(string $key, int $length, string $value): bool
	{
		if ($length < mb_strlen($value)) {
			$this->callback->receiveError($key, self::KIND_LENGTH, ['safe-length' => $length, 'error-length' => mb_strlen($value)]);
			return false;
		}

		return true;
	}
}
