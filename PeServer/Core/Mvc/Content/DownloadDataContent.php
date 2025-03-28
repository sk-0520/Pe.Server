<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\StaticDataContent;

/**
 * ダウンロード用データ。
 */
class DownloadDataContent extends StaticDataContent implements IDownloadContent
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param non-empty-string $fileName
	 * @param string|array<mixed>|Binary|Stream $data
	 */
	public function __construct(
		string $mime,
		private readonly string $fileName,
		string|array|Binary|Stream $data
	) {
		parent::__construct(HttpStatus::OK, $mime, $data);
	}

	#region IDownloadContent

	public function getFileName(): string
	{
		return $this->fileName;
	}

	#endregion
}
