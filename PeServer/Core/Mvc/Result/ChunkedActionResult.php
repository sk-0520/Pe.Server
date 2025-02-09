<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Binary;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\ChunkedContentBase;
use PeServer\Core\Mvc\Content\DataContent;
use PeServer\Core\Mvc\Content\DownloadDataContent;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Serialization\Json;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * 結果操作: データ。
 */
readonly class ChunkedActionResult implements IActionResult
{
	/**
	 * 生成。
	 *
	 * @param ChunkedContentBase $content
	 */
	public function __construct(
		private ChunkedContentBase $content
	) {
		//NOP
	}

	#region IActionResult

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = HttpStatus::OK;

		$response->header->setContentType(ContentType::create($this->content->mime));

		$response->header->addValue('Transfer-Encoding', "chunked");

		$response->content = $this->content;

		return $response;
	}

	#endregion
}
