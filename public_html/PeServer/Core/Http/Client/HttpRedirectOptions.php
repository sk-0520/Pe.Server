<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

/**
 * HttpClient リダイレクト設定データ。
 */

readonly class HttpRedirectOptions
{
	#region define

	public const DEFAULT_COUNT = 20;
	public const DEFAULT_AUTO_REFERER = true;

	#endregion

	/**
	 * 生成
	 *
	 * @param bool $isEnabled リダイレクトするか
	 * @param int $count リダイレクト最大回数
	 * @param bool $autoReferer リファラを付与するか
	 */
	public function __construct(
		public bool $isEnabled = true,
		public int $count = self::DEFAULT_COUNT,
		public bool $autoReferer = self::DEFAULT_AUTO_REFERER
	) {
	}
}
