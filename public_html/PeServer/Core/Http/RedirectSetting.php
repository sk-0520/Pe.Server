<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Http\HttpStatus;

class RedirectSetting
{
	/**
	 * 生成。
	 *
	 * @param string $url リダイレクト先
	 * @param HttpStatus $status リダイレクト時のHTTPステータスコード。
	 */
	public function __construct(
		/** @readonly */
		public string $url,
		/** @readonly */
		public HttpStatus $status
	) {
	}
}
