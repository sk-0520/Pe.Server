<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;

/**
 * ストア設定。
 *
 * @immutable
 */
class StoreOptions
{
	public function __construct(
		public CookieOption $cookie,
		public TemporaryOption $temporary,
		public SessionOption $session
	) {
	}

	public static function default(): self
	{
		$tempDirPath = Directory::getTemporaryDirectory();
		$cookieOption = new CookieOption('', null, true, true, 'Lax');

		return new self(
			$cookieOption,
			new TemporaryOption(TemporaryOption::DEFAULT_NAME, Path::combine($tempDirPath, TemporaryOption::DEFAULT_NAME), $cookieOption),
			new SessionOption(SessionOption::DEFAULT_NAME, Path::combine($tempDirPath, SessionOption::DEFAULT_NAME), $cookieOption)
		);
	}
}
