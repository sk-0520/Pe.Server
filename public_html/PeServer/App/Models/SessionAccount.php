<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;

/**
 * @immutable
 */
class SessionAccount
{
	/**
	 * 生成。
	 *
	 * @param string $userId
	 * @param string $loginId
	 * @param string $name
	 * @param string $level
	 * @phpstan-param UserLevel::* $level
	 * @param string $state
	 * @phpstan-param UserState::* $state
	 */
	public function __construct(
		public string $userId,
		public string $loginId,
		public string $name,
		public string $level,
		public string $state
	) {
	}
}
