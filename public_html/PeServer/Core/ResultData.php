<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 結果データ。
 *
 * @template TValue
 * @immutable
 */
final class ResultData
{
	/**
	 * 成功状態。
	 */
	public bool $success;
	/**
	 * 成功時のデータ。
	 *
	 * @phpstan-var TValue
	 */
	public mixed $value;

	private function __construct(bool $success, mixed $value)
	{
		$this->success = $success;
		$this->value = $value;
	}

	/**
	 * 成功データの生成。
	 *
	 * @template TResultValue
	 * @param mixed $value 成功データ。
	 * @phpstan-param TResultValue $value
	 * @return ResultData
	 * @phpstan-return ResultData<TResultValue>
	 */
	public static function createSuccess(mixed $value): ResultData
	{
		return new ResultData(true, $value);
	}

	/**
	 * 失敗データの生成。
	 *
	 * @return ResultData
	 * @phpstan-return ResultData<mixed>
	 */
	public static function createFailure(): ResultData
	{
		return new ResultData(false, null);
	}
}
