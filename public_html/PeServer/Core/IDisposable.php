<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 解放処理用インターフェイス。
 *
 * Code::using でうまいことやりたいのだ。
 */
interface IDisposable
{
	/**
	 * 解放済みか。
	 *
	 * @return boolean
	 */
	public function isDisposed(): bool;

	/**
	 * 解放処理。
	 *
	 * @return void
	 */
	public function dispose(): void;
}
