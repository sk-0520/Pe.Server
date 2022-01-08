<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Throws\ArgumentException;

class RedirectActionResult implements IActionResult
{
	private string $url;
	private ?HttpStatus $status;

	public function __construct(string $url, ?HttpStatus $status = null)
	{
		if (StringUtility::isNullOrWhiteSpace($url)) {
			throw new ArgumentException('$url');
		}

		$this->url = $url;
		$this->status = $status;
	}

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();
		$status = $this->status ?? HttpStatus::found();

		$response->status = $status;
		$response->header->setRedirect($this->url, $status);

		return $response;
	}
}
