<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\OutputBufferException;

/**
 * 出力バッファリング。
 *
 * 出力バッファリング系を統括してとりあえず出力されるものをまとめる。
 * このクラスは部分的にあれこれするのではなく一括でどさっと処理する想定。
 *
 * @see https://www.php.net/manual/function.ob-start.php
 * @see https://www.php.net/manual/function.ob-get-contents.php
 * @see https://www.php.net/manual/function.ob-end-clean.php
 */
class OutputBuffer extends DisposerBase
{
	/**
	 * 生成しつつバッファリング開始。
	 * @throws OutputBufferException バッファリング開始失敗。
	 */
	public function __construct()
	{
		if (!ob_start()) {
			throw new OutputBufferException('ob_start');
		}
	}

	#region function

	/**
	 * バッファリング中のデータ取得。
	 *
	 * データは破棄される。
	 *
	 * @return Binary バッファリング中のデータ。
	 * @throws OutputBufferException 取得失敗。
	 */
	public function getContents(): Binary
	{
		$buffer = ob_get_contents();
		if ($buffer === false) {
			throw new OutputBufferException('ob_get_contents');
		}

		return new Binary($buffer);
	}

	/**
	 * バッファリング中のバイト数を取得。
	 *
	 * @see https://www.php.net/manual/function.ob-get-length.php
	 * @return int バイト数。
	 * @throws OutputBufferException 取得失敗。
	 */
	public function getByteCount(): int
	{
		$result = ob_get_length();
		if ($result === false) {
			throw new OutputBufferException('ob_get_length');
		}

		return $result;
	}

	/**
	 * 引数処理中の出力を取得。
	 *
	 * **基本的にこれだけ使ってればいい。**
	 *
	 * @param callable $action 出力を取得したい処理。
	 * @return Binary 取得した処理。
	 * @throws OutputBufferException なんかあかんかった。
	 */
	public static function get(callable $action): Binary
	{
		$self = new self();

		try {
			$action();
			return $self->getContents();
		} finally {
			$self->dispose();
		}
	}

	#endregion

	#region DisposerBase

	/**
	 * @inheritdoc
	 * @throws OutputBufferException クリーンアップ失敗。
	 */
	protected function disposeImpl(): void
	{
		if (!ob_end_clean()) {
			throw new OutputBufferException('ob_end_clean');
		}

		parent::disposeImpl();
	}

	#endregion
}
