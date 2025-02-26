<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Text;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Throws\ArgumentException;

/**
 * セッション設定。
 */
readonly class SessionOptions
{
	#region define

	/** セッションID保持Cookie名。 */
	public const DEFAULT_NAME = 'PHPSESSID';
	public const DEFAULT_PATH = './session';

	#endregion

	/**
	 * 生成。
	 *
	 * @param non-empty-string $name セッション名。
	 * @param string $savePath 保存場所。
	 * @param string $handler セッションハンドラー。
	 * @phpstan-param "file"|"sqlite" $handler セッションハンドラー。
	 * @param CookieOptions $cookie クッキー設定。
	 */
	public function __construct(
		public string $name,
		public string $savePath,
		public ?string $handler,
		public CookieOptions $cookie
	) {
		if (Text::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$name');
		}
	}
}
