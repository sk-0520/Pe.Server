<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Web\Url;

/**
 * 結果操作: リダイレクト。
 */
readonly class RedirectActionResult implements IActionResult
{
	public function __construct(
		private Url $url,
		private ?HttpStatus $status = null
	) {
	}

	#region IActionResult

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();
		$status = $this->status ?? HttpStatus::Found;

		$response->status = $status;
		$response->header->setRedirect($this->url, $status);

		return $response;
	}

	#endregion
}
