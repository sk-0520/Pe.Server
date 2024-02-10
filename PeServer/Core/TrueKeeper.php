<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\MagicPropertyException;

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
 * @property-read bool $latest 最終設定値。
 */
final class TrueKeeper
{
	#region variable

	private bool $state = true;

	private bool $latest = false;

	#endregion

	#region get/set

	public function __set(string $name, bool $value): void
	{
		if ($name !== 'state') {
			throw new MagicPropertyException($name);
		}

		$this->latest = $value;
		if (!$value) {
			$this->state = false;
		}
	}

	public function __get(string $name): bool
	{
		if ($name === 'state') {
			return $this->state;
		}
		if ($name === 'latest') {
			return $this->latest;
		}

			throw new MagicPropertyException($name);
	}

	#endregion
}
