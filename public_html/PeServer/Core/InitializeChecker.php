<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\InvalidOperationException;

/**
 * 初期化状態チェック処理。
 *
 * 静的初期化が必要なクラスに対する防御として構築しており、使用側でも定型の呼び出しが必要となる。
 */
final class InitializeChecker
{
	#region variable

	/**
	 * 初期化済みか。
	 */
	private bool $isInitialized  = false;

	#endregion

	#region function

	/**
	 * 初期化処理。
	 *
	 * すでに初期化されている場合は例外が投げられる。
	 *
	 * @throws InvalidOperationException 既に初期化されている。
	 */
	public function initialize(): void
	{
		if ($this->isInitialized) {
			throw new InvalidOperationException('initialized');
		}

		$this->isInitialized = true;
	}

	/**
	 * 初期化されていない場合に例外を投げる。
	 *
	 * @throws InvalidOperationException 初期化されていない。
	 */
	public function throwIfNotInitialize(): void
	{
		if (!$this->isInitialized) {
			throw new InvalidOperationException('not initialize');
		}
	}

	#endregion
}
