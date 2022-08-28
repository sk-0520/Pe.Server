<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\OutputBufferException;

/**
 * 出力。
 *
 * TODO: コンストラクタで開始する方針に変更予定。 `get` の挙動に変更はなし。
 */
abstract class OutputBuffer
{
	#region function

	private static function begin(): void
	{
		if (!ob_start()) {
			throw new OutputBufferException('ob_start');
		}
	}

	private static function end(): void
	{
		if (!ob_end_clean()) {
			throw new OutputBufferException('ob_end_clean');
		}
	}

	private static function getContents(): Binary
	{
		$buffer = ob_get_contents();
		if ($buffer === false) {
			throw new OutputBufferException('ob_get_contents');
		}

		return new Binary($buffer);
	}

	/**
	 * 引数処理中の出力を取得。
	 *
	 * @param callable $action 出力を取得したい処理。
	 * @return Binary 取得した処理。
	 * @throws OutputBufferException なんかあかんかった。
	 */
	public static function get(callable $action): Binary
	{
		self::begin();

		try {
			$action();
			return self::getContents();
		} finally {
			self::end();
		}
	}

	#endregion
}
