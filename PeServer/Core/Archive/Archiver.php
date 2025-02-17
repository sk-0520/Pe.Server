<?php

declare(strict_types=1);

namespace PeServer\Core\Archive;

use PeServer\Core\Binary;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Throws\ArchiveException;
use PeServer\Core\Throws\Throws;
use ValueError;
use ZipArchive;

/**
 * アーカイブ処理。
 */
abstract class Archiver
{
	#region define

	/**
	 * GZIP: デフォルト。
	 *
	 * `FORCE_GZIP` ラッパー。
	 */
	public const GZIP_DEFAULT = FORCE_GZIP;
	/**
	 * GZIP: RFC 1950 準拠。
	 *
	 * `FORCE_DEFLATE` ラッパー。
	 */
	public const GZIP_DEFLATE = FORCE_DEFLATE;

	#endregion

	#region function

	/**
	 * GZIP圧縮処理。
	 *
	 * `gzencode` ラッパー。
	 *
	 * @param Binary $data 圧縮するデータ。
	 * @param -1|0|1|2|3|4|5|6|7|8|9 $level 圧縮レベル。
	 * @param int $encoding `self::GZIP_DEFAULT` か `self::FORCE_DEFLATE` を指定。
	 * @phpstan-param self::GZIP_* $encoding
	 * @return Binary 圧縮データ。
	 * @throws ArchiveException 失敗。
	 * @see https://www.php.net/manual/function.gzencode.php
	 */
	public static function compressGzip(Binary $data, int $level = -1, int $encoding = self::GZIP_DEFAULT): Binary
	{
		$result = Throws::wrap(ValueError::class, ArchiveException::class, fn() => gzencode($data->raw, $level, $encoding));
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
		$result = ErrorHandler::trap(fn() => gzdecode($data->raw));
		if (!$result->success) {
			throw new ArchiveException();
		}

		return new Binary($result->value);
	}


	/**
	 * 簡易ZIPファイル作成処理。
	 *
	 * @param string $zipPath ZIPファイルの作成先パス。
	 * @param ArchiveEntry[] $entryFiles
	 * @throws ArchiveException ZIPファイルを新規作成できない
	 */
	public static function compressZip(string $zipPath, array $entryFiles): void
	{
		$zipArchive = new ZipArchive();
		$zipErrorCode = 0;
		// 空ファイルで Using empty file as ZipArchive is deprecated が出るため trap で抑制
		ErrorHandler::trap(function () use ($zipArchive, $zipPath, &$zipErrorCode) {
			$zipErrorCode = $zipArchive->open($zipPath, ZipArchive::CREATE | ZipArchive::EXCL | ZipArchive::CHECKCONS);
			return $zipErrorCode;
		});
		if ($zipErrorCode !== true) {
			throw new ArchiveException("code: {$zipErrorCode}");
		}
		if (empty($entryFiles)) {
			throw new ArchiveException('empty: $entryFiles');
		}

		foreach ($entryFiles as $entryFile) {
			$zipArchive->addFile($entryFile->path, $entryFile->entry);
		}

		$zipArchive->close();
	}

	#endregion
}
