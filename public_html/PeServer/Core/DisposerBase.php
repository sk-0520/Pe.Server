<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ObjectDisposedException;

/**
 * 解放処理用基盤クラス。
 */
abstract class DisposerBase implements IDisposable
{
	#region variable

	/** 解放済みか。 */
	private bool $isDisposed = false;

	#endregion

	#region function

	/**
	 * 何もしない解放処理オブジェクトを生成。
	 */
	public static function empty(): IDisposable
	{
		return new LocalEmptyDisposer();
	}

	final public function __destruct()
	{
		$this->dispose();
	}

	/**
	 * 解放済みの場合、例外を投げる。
	 *
	 * @return void
	 * @throws ObjectDisposedException
	 */
	protected final function throwIfDisposed(): void
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
		//NONE
	}

	#endregion

	#region IDisposable

	public function isDisposed(): bool
	{
		return $this->isDisposed;
	}

	public final function dispose(): void
	{
		if ($this->isDisposed()) {
			return;
		}

		$this->disposeImpl();

		$this->isDisposed = true;
	}

	#endregion
}

final class LocalEmptyDisposer extends DisposerBase
{
	//NONE
}
