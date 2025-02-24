<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FileNotFoundException;

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
	public const SVG = 'image/svg+xml';
	public const EVENT_STREAM = 'text/event-stream';

	#endregion

	#region function

	/**
	 * ファイルからMIMEを取得。
	 *
	 * `mime_content_type` ラッパー。
	 *
	 * @param string $fileName
	 * @return non-empty-string
	 * @throws ArgumentException
	 * @throws FileNotFoundException
	 * @see https://php.net/manual/function.mime-content-type.php
	 */
	public static function fromFileName(string $fileName): string
	{
		if (Text::isNullOrWhiteSpace($fileName)) {
			throw new ArgumentException($fileName);
		}

		$result = ErrorHandler::trap(fn () =>  mime_content_type($fileName));
		if (!$result->success || Text::isNullOrEmpty($result->value)) {
			throw new FileNotFoundException($fileName);
		}

		return $result->value;
	}

	#endregion
}
