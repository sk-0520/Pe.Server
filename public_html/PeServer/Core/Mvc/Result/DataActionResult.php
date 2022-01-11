<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Throws\NotImplementedException;


class DataActionResult implements IActionResult
{
	private DataContent $content;

	/**
	 * 生成。
	 *
	 * @param DataContent $content
	 */
	public function __construct(DataContent $content)
	{
		$this->content = $content;
	}

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = $this->content->httpStatus;

		$response->header->addValue('Content-Type', $this->content->mime);

		// json_encodeは後で対応する
		$response->content = match ($this->content->mime) {
			Mime::TEXT => strval($this->content->data),
			Mime::JSON => json_encode($this->content->data),
			default => throw new NotImplementedException(),
		};

		return $response;
	}
}
