<?php

declare(strict_types=1);

namespace PeServer\App\Models;

/**
 * 匿名セッション情報。
 *
 * 今時点の作業状態が真になる。
 */
readonly class SessionAnonymous
{
	public function __construct(
		public bool $login = false,
		public bool $signup1 = false,
		public bool $signup2 = false,
		public bool $passwordReminder = false,
		public bool $passwordReset = false,
	) {
	}
}
