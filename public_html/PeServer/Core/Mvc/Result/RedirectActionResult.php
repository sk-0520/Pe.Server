<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;

/**
 * 結果操作: リダイレクト。
 *
 * @immutable
 */
class RedirectActionResult implements IActionResult
{
	public function __construct(
		private string $url,
		private ?HttpStatus $status = null
	) {
		if (StringUtility::isNullOrWhiteSpace($url)) {
			throw new ArgumentException('$url');
		}
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
