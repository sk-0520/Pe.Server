<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\OutputBufferException;

/**
 * 出力バッファリング。
 */
class OutputBuffer extends DisposerBase
{
	public function __construct()
	{
		if (!ob_start()) {
			throw new OutputBufferException('ob_start');
		}
	}

	#region function

	public function getContents(): Binary
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

	protected function disposeImpl(): void
	{
		if (!ob_end_clean()) {
			throw new OutputBufferException('ob_end_clean');
		}

		parent::disposeImpl();
	}

	#endregion
}
