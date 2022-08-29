<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * ロジック呼び出し。
 */
abstract class LogicCallMode
{
	#region define

	/**
	 * 初期化状態生値。
	 */
	protected const INITIALIZE = 0;
	/**
	 * 確定状態生値。
	 */
	protected const SUBMIT = 1;

	#endregion

	#region function

	/**
	 * 初期化処理か。
	 *
	 * isSubmit と同条件になることはない。
	 *
	 * @return boolean
	 */
	public abstract function isInitialize(): bool;

	/**
	 * 確定処理か。
	 *
	 * isInitialize と同条件になることはない。
	 *
	 * @return boolean
	 */
	public abstract function isSubmit(): bool;

	/**
	 * 処理状態生成。
	 *
	 * @param integer $mode 状態生値。
	 * @phpstan-param self::INITIALIZE|self::SUBMIT $mode 状態生値。
	 * @return LogicCallMode
	 */
	private static function create(int $mode): LogicCallMode
	{
		return new LocalLogicCallModeImpl($mode);
	}

	/**
	 * 初期化処理状態生成。
	 *
	 * @return LogicCallMode
	 */
	public static function initialize(): LogicCallMode
	{
		return self::create(self::INITIALIZE);
	}

	/**
	 * 確定処理状態生成。
	 *
	 * @return LogicCallMode
	 */
	public static function submit(): LogicCallMode
	{
		return self::create(self::SUBMIT);
	}

	#endregion
}

final class LocalLogicCallModeImpl extends LogicCallMode
{
	/**
	 * 呼び出し方法。
	 * @readonly
	 * @phpstan-var parent::INITIALIZE|parent::SUBMIT $mode
	 */
	private int $mode;

	/**
	 * 生成。
	 *
	 * @param integer $mode
	 * @phpstan-param parent::INITIALIZE|parent::SUBMIT $mode
	 */
	public function __construct(int $mode)
	{
		$this->mode = $mode;
	}

	public function isInitialize(): bool
	{
		return $this->mode === self::INITIALIZE;
	}

	public function isSubmit(): bool
	{
		return $this->mode === self::SUBMIT;
	}
}
