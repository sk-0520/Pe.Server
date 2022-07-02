<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Http\HttpResponse;

/**
 * アクションメソッドの結果操作。
 */
interface IActionResult
{
	/**
	 * 結果操作からHTTPレスポンスを生成。
	 */
	public function createResponse(): HttpResponse;

}
