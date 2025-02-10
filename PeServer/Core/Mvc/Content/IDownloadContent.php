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
interface IDownloadContent
{
	#region function

	/**
	 * ファイル名を取得。
	 *
	 * @return non-empty-string
	 */
	public function getFileName(): string;

	#endregion
}
