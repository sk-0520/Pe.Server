<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

/**
 * 真の状態を保持する。
 *
 * 一度でも偽になったら真には復帰しない。
 * 検証処理を連続で行うことを想定。
 *
 * MEMO:
 *  $v &= true
 *  $v &= false
 *  ていうよくやるやつが動かんかってん
 *
 * @property bool $state 真偽値を何も考えずに代入する。取得した際に一度でも偽が代入されていれば偽になる。
 * @property bool $last 最終設定値。
 */
class TrueKeeper
{
	private bool $state = true;

	private bool $last = false;

	public function __set(string $name, bool $value): void
	{
		if ($name !== 'state') {
			throw new ArgumentException();
		}

		$this->last = $value;
		if (!$value) {
			$this->state = false;
		}
	}

	public function __get(string $name): bool
	{
		if ($name === 'state') {
			return $this->state;
		}
		if ($name === 'last') {
			return $this->last;
		}

		throw new ArgumentException($name);
	}
}
