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
	 * `gzencode` ラッパー。
	 *
	 * @param Binary $data 圧縮するデータ
	 * @param integer $level 圧縮レベル。
	 * @param integer $encoding gzencode(encoding:)
	 * @return Binary 圧縮データ。
	 * @throws ArchiveException 失敗。
	 * @see https://www.php.net/manual/function.gzencode.php
	 */
	public static function compressGzip(Binary $data, int $level = -1, int $encoding = FORCE_GZIP): Binary
	{
		$result = gzencode($data->getRaw(), $level, $encoding);
		if ($result === false) {
			throw new ArchiveException();
		}

		return new Binary($result);
	}

	/**
	 * GZIP展開処理。
	 *
	 * `gzdecode` ラッパー。
	 *
	 * @param Binary $data 圧縮データ。
	 * @return Binary 展開データ。
	 * @throws ArchiveException 失敗。
	 * @see https://www.php.net/manual/function.gzdecode.php
	 */
	public static function extractGzip(Binary $data): Binary
	{
		$result = gzdecode($data->getRaw());
		if ($result === false) {
			throw new ArchiveException();
		}

		return new Binary($result);
	}
}
