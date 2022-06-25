<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ObjectDisposedException;

abstract class DisposerBase implements IDisposable
{
	private bool $isDisposed = false;

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
