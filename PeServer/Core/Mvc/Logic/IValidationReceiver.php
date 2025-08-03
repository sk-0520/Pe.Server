<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Logic;

interface IValidationReceiver
{
	#region function

	/**
	 * エラーメッセージを受領。
	 *
	 * @param string $key
	 * @param string $message
	 */
	public function receiveErrorMessage(string $key, string $message): void;

	/**
	 * 検証結果の失敗受領。
	 *
	 * @param string $key
	 * @param int $kind
	 * @param array<array-key,int|string> $parameters
	 */
	public function receiveErrorKind(string $key, int $kind, array $parameters): void;

	#endregion
}
