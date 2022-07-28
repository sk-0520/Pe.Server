<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ObjectDisposedException;

/**
 * 解放処理用基盤クラス。
 */
abstract class DisposerBase implements IDisposable
{
	/** 解放済みか。 */
	private bool $isDisposed = false;

	/**
	 * 何もしない解放処理オブジェクトを生成。
	 */
	public function empty(): IDisposable
	{
		return new LocalEmptyDisposer();
	}

	public function __destruct()
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

	/** 解放済みか。 */
	public function isDisposed(): bool
	{
		return $this->isDisposed;
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

	public final function dispose(): void
	{
		if ($this->isDisposed()) {
			return;
		}

		$this->disposeImpl();

		$this->isDisposed = true;
	}
}

final class LocalEmptyDisposer extends DisposerBase
{
	protected function disposeImpl(): void
	{
		//NONE
	}
}
