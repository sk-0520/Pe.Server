<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\StaticDataContent;

/**
 * ダウンロード用データ。
 */
class DownloadDataContent extends StaticDataContent implements IDownloadContent
{
	#region variable

	/** @var non-empty-string */
	private readonly string $fileName;

	#endregion

	/**
	 * 生成。
	 *
	 * @param non-empty-string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param non-empty-string $fileName
	 * @param string|Binary $data
	 */
	public function __construct(string $mime, string $fileName, string|Binary $data)
	{
		parent::__construct(HttpStatus::OK, $mime, $data);
		$this->fileName = $fileName;
	}

	#region IDownloadContent

	public function getFileName(): string
	{
		return $this->fileName;
	}

	#endregion
}
