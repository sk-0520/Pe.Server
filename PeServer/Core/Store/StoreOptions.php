<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Store\SessionOptions;
use PeServer\Core\Store\TemporaryOptions;

/**
 * ストア設定。
 */
readonly class StoreOptions
{
	public function __construct(
		public CookieOptions $cookie,
		public TemporaryOptions $temporary,
		public SessionOptions $session
	) {
	}

	#region function

	public static function default(): self
	{
		$tempDirPath = Directory::getTemporaryDirectory();
		$cookieOption = new CookieOptions('./cookie', null, true, true, 'Lax');

		return new self(
			$cookieOption,
			new TemporaryOptions(TemporaryOptions::DEFAULT_NAME, Path::combine($tempDirPath, TemporaryOptions::DEFAULT_NAME), $cookieOption),
			new SessionOptions(SessionOptions::DEFAULT_NAME, Path::combine($tempDirPath, SessionOptions::DEFAULT_NAME), $cookieOption)
		);
	}

	#endregion
}
