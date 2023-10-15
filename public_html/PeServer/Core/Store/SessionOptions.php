<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Text;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Throws\ArgumentException;

/**
 * セッション設定。
 *
 * @immutable
 */
class SessionOptions
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
	 * @param CookieOptions $cookie クッキー設定。
	 */
	public function __construct(
		public string $name,
		public string $savePath,
		public CookieOptions $cookie
	) {
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$name');
		}

		$this->name = $name;
		$this->savePath = $savePath;
		$this->cookie = $cookie;
	}
}
