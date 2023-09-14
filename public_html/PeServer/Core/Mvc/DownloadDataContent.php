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
class DownloadDataContent extends DataContent
{
	#region variable

	/** @readonly */
	public string $fileName;

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param Binary $data
	 */
	public function __construct(string $mime, string $fileName, Binary $data)
	{
		parent::__construct(HttpStatus::None, $mime, $data);
		$this->fileName = $fileName;
	}
}
