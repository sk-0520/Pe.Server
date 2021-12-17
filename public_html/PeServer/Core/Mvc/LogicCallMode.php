<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * ロジック呼び出し。
 */
abstract class LogicCallMode
{
	/**
	 * 初期化状態生値。
	 */
	protected const INITIALIZE = 0;
	/**
	 * 確定状態生値。
	 */
	protected const SUBMIT = 1;

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
	 * @return LogicCallMode
	 */
	private static function create(int $mode): LogicCallMode
	{
		return new _LogicCallMode_Invisible($mode);
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
}

final class _LogicCallMode_Invisible extends LogicCallMode
{
	/**
	 * 呼び出し方法。
	 *
	 * @var int
	 */
	private $_mode;

	public function __construct(int $mode)
	{
		$this->_mode = $mode;
	}

	public function isInitialize(): bool
	{
		return $this->_mode === self::INITIALIZE;
	}

	public function isSubmit(): bool
	{
		return $this->_mode === self::SUBMIT;
	}
}
