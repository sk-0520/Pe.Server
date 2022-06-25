<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\DataContent;

class DownloadDataContent extends DataContent
{
	public string $fileName;

	/**
	 * 生成。
	 *
	 * @param string $mime
	 * @param Binary $data
	 */
	public function __construct(string $mime, string $fileName, Binary $data)
	{
		parent::__construct(HttpStatus::none(), $mime, $data);
		$this->fileName = $fileName;
	}
}
