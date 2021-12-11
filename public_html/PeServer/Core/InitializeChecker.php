<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;

/**
 * 初期化状態チェック処理。
 *
 * 静的初期化が必要なクラスに対する防御として構築しており、使用側でも定型の呼び出しが必要となる。
 */
final class InitializeChecker
{
	private $isInitialized  = false;

	/**
	 * 初期化処理。
	 *
	 * すでに初期化されている場合は例外が投げられる。
	 *
	 * @return void
	 *
	 * @throws Exception 既に初期化されている。
	 */
	public function initialize()
	{
		if ($this->isInitialized) {
			throw new Exception('initialized');
		}

		$this->isInitialized = true;
	}

	/**
	 * 初期化されていない場合に例外を投げる。
	 *
	 * @return void
	 *
	 * @throws Exception 初期化されていない。
	 */
	public function throwIfNotInitialize()
	{
		if (!$this->isInitialized) {
			throw new Exception('not initialize');
		}
	}
}