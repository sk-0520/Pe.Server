<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;

/**
 * ダウンロード用データ。
 */
readonly class DownloadDataContent extends DataContent
{
	#region variable

	public readonly string $fileName;

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param string|Binary $data
	 */
	public function __construct(string $mime, string $fileName, string|Binary $data)
	{
		parent::__construct(HttpStatus::None, $mime, $data);
		$this->fileName = $fileName;
	}
}
