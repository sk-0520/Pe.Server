<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\OutputBufferException;

/**
 * 出力。
 */
abstract class OutputBuffer
{
	/**
	 * 引数処理中の出力を取得。
	 *
	 * @param callable $action 出力を取得したい処理。
	 * @return Binary 取得した処理。
	 * @throws OutputBufferException なんかあかんかった。
	 */
	public static function get(callable $action): Binary
	{
		if (!ob_start()) {
			throw new OutputBufferException('ob_start');
		}
		try {
			$action();
			$buffer = ob_get_contents();
			if ($buffer === false) {
				throw new OutputBufferException('ob_get_contents'); // @phpstan-ignore-line This throw is overwritten by a different one in the finally block below.
			}
			return new Binary($buffer); // @phpstan-ignore-line This throw is overwritten by a different one in the finally block below.
		} finally {
			if (!ob_end_clean()) {
				throw new OutputBufferException('ob_end_clean');  // @phpstan-ignore-line The overwriting throw is on this line.
			}
		}
	}
}
