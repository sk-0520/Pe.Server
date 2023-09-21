<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

/**
 * まいむ。
 *
 * あれこれ使いまわすやつの入力ミスを減らすために定義してるだけ。
 * 大量に定義して管理するようなことはしない。
 */
abstract class Mime
{
	#region define

	public const TEXT = 'text/plain';
	public const HTML = 'text/html';
	public const JSON = 'application/json';
	public const GZ = 'application/gzip';
	public const ZIP = 'application/zip';
	public const STREAM = 'application/octet-stream';
	public const FORM = 'application/x-www-form-urlencoded';
	public const ICON = 'image/vnd.microsoft.icon';
	public const SQLITE3 = 'application/x-sqlite3';

	#endregion

	#region function

	/**
	 * ファイル名からMIMEを取得。
	 *
	 * `mime_content_type` ラッパー。
	 *
	 * @param string $fileName
	 * @return string
	 * @phpstan-return non-empty-string
	 * @throws ArgumentException
	 * @see https://php.net/manual/function.mime-content-type.php
	 */
	public static function fromFileName(string $fileName): string
	{
		$result =  mime_content_type($fileName);
		if ($result === false || Text::isNullOrEmpty($result)) {
			throw new ArgumentException($fileName);
		}

		/** @phpstan-var non-empty-string */
		return $result;
	}

	#endregion
}
