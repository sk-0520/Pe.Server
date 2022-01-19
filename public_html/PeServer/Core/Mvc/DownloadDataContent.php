<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Bytes;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\DataContent;

class DownloadDataContent extends DataContent
{
	public string $fileName;

	/**
	 * 生成。
	 *
	 * @param string $mime
	 * @param Bytes $data
	 */
	public function __construct(string $mime, string $fileName, Bytes $data)
	{
		parent::__construct(HttpStatus::none(), $mime, $data);
		$this->fileName = $fileName;
	}
}
