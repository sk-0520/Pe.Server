<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ObjectDisposedException;

/**
 * 解放処理用基底クラス。
 */
abstract class DisposerBase implements IDisposable
{
	#region variable

	/** 解放済みか。 */
	private bool $isDisposed = false;

	#endregion

	final public function __destruct()
	{
		$this->dispose();
	}

	#region function

	/**
	 * 何もしない解放処理オブジェクトを生成。
	 */
	public static function empty(): IDisposable
	{
		return new LocalEmptyDisposer();
	}

	/**
	 * 解放済みの場合、例外を投げる。
	 *
	 * @return void
	 * @throws ObjectDisposedException
	 */
	final protected function throwIfDisposed(): void
	{
		if ($this->isDisposed()) {
			throw new ObjectDisposedException();
		}
	}

	/**
	 * 解放処理内部実装。
	 *
	 * 継承先で継承元を呼び出すこと。
	 *
	 * @return void
	 */
	protected function disposeImpl(): void
	{
		//NOP
	}

	#endregion

	#region IDisposable

	public function isDisposed(): bool
	{
		return $this->isDisposed;
	}

	final public function dispose(): void
	{
		if ($this->isDisposed()) {
			return;
		}

		$this->disposeImpl();

		$this->isDisposed = true;
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
final class LocalEmptyDisposer extends DisposerBase
{
	//NOP
}
