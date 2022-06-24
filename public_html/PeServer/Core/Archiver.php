<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArchiveException;

/**
 * アーカイブ処理。
 */
abstract class Archiver
{
	/**
	 * GZIP圧縮処理。
	 *
	 * @param Binary $data 圧縮するデータ
	 * @param integer $level 圧縮レベル。
	 * @param integer $encoding gzencode(encoding:)
	 * @return Binary
	 * @throws ArchiveException 失敗。
	 */
	public static function compressGzip(Binary $data, int $level = -1, int $encoding = FORCE_GZIP): Binary
	{
		$result = gzencode($data->getRaw(), $level, $encoding);
		if ($result === false) {
			throw new ArchiveException();
		}

		return new Binary($result);
	}
}
