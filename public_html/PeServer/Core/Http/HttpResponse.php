<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;

/**
 * HTTP応答データ。
 */
class HttpResponse
{
	#region variable

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
	 * @readonly
	 */
	public HttpHeader $header;

	#endregion

	/**
	 * 応答本文。
	 *
	 * @var string|Binary|ICallbackContent|null
	 */
	public string|Binary|ICallbackContent|null $content = null;

	public function __construct()
	{
		$this->status = HttpStatus::None;
		$this->header = new HttpHeader();
	}
}
