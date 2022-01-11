<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Bytes;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;


/**
 * HTTP応答データ。
 */
class HttpResponse
{
	/**
	 * 応答HTTPステータスコード。
	 *
	 * @var HttpStatus
	 */
	public HttpStatus $status;

	/**
	 * 応答ヘッダ。
	 *
	 * @var HttpHeader
	 */
	public HttpHeader $header;


	/**
	 * 応答本文。
	 *
	 * @var string|Bytes|ICallbackContent|null
	 */
	public string|Bytes|ICallbackContent|null $content = null;

	public function __construct()
	{
		$this->status = HttpStatus::none();
		$this->header = new HttpHeader();
	}
}
