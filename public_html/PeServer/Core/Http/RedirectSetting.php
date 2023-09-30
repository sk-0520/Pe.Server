<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Web\Url;

/**
 * リダイレクト設定。
 *
 * @immutable
 */
class RedirectSetting
{
	/**
	 * 生成。
	 *
	 * @param Url $url リダイレクト先
	 * @param HttpStatus $status リダイレクト時のHTTPステータスコード。
	 */
	public function __construct(
		public Url $url,
		public HttpStatus $status
	) {
	}
}
