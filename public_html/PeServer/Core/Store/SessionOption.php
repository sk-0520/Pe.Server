<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Text;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\ArgumentException;

/**
 * セッション設定。
 *
 * @immutable
 */
class SessionOption
{
	#region define

	/** セッションID保持Cookie名。 */
	public const DEFAULT_NAME = 'PHPSESSID';

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $name セッション名。
	 * @phpstan-param non-empty-string $name
	 * @param string $savePath 保存場所。
	 * @param CookieOption $cookie クッキー設定。
	 */
	public function __construct(
		public string $name,
		public string $savePath,
		public CookieOption $cookie
	) {
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line
			throw new ArgumentException('$name');
		}

		$this->name = $name;
		$this->savePath = $savePath;
		$this->cookie = $cookie;
	}
}
