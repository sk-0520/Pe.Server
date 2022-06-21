<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 結果データ。
 */
final class ResultData
{
	/** 成功状態。 */
	public bool $success = false;
	/** 成功時のデータ。 */
	public mixed $value = null;

	private function __construct(bool $success, mixed $value)
	{
		$this->success = $success;
		$this->value = $value;
	}

	/**
	 * 成功データの生成。
	 *
	 * @param mixed $value 成功データ。
	 * @return ResultData
	 */
	public static function createSuccess(mixed $value): ResultData
	{
		return new ResultData(true, $value);
	}

	/**
	 * 失敗データの生成。
	 *
	 * @return ResultData
	 */
	public static function createFailure(): ResultData
	{
		return new ResultData(false, null);
	}
}
