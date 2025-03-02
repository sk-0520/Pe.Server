<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Errors\ErrorData;

/**
 * 結果データ。
 *
 * @template TValue
 */
final readonly class ResultData
{
	#region variable

	/**
	 * 成功状態。
	 */
	public bool $success;
	/**
	 * 成功時のデータ。
	 *
	 * @phpstan-var ($success is true ? TValue : mixed)
	 */
	public mixed $value;

	/**
	 * 失敗時のエラー内容。
	 *
	 * @var ErrorData
	 */
	public ErrorData|null $error;

	#endregion

	private function __construct(bool $success, mixed $value, ErrorData|null $error)
	{
		$this->success = $success;
		$this->value = $value;
		$this->error = $error;
	}

	#region function

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
		return new ResultData(true, $value, null);
	}

	/**
	 * 失敗データの生成。
	 *
	 * @return ResultData
	 * @phpstan-return ResultData<mixed>
	 */
	public static function createFailure(ErrorData|null $error): ResultData
	{
		return new ResultData(false, null, $error);
	}

	/**
	 * 結果が失敗か失敗対象の値か。
	 * @param mixed $failValue 失敗の対象とする値。
	 * @return bool 真: 失敗。
	 * @phpstan-pure
	 */
	public function isFailureOrFailValue(mixed $failValue): bool
	{
		return !$this->success || $this->value === $failValue;
	}

	/**
	 * 結果が失敗か `false` か。
	 * @return bool 真: 失敗。
	 * @phpstan-pure
	 */
	public function isFailureOrFalse(): bool
	{
		return $this->isFailureOrFailValue(false);
	}


	#endregion
}
