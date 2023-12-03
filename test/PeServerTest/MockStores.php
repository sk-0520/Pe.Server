<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Domain\UserState;

class MockStores
{
	public const SESSION_ACCOUNT_USER_ID = 'session-account-user-id';
	public const SESSION_ACCOUNT_LOGIN_ID = 'session-account-login-id';
	public const SESSION_ACCOUNT_NAME = 'session-account-name';
	public const SESSION_ACCOUNT_STATE = UserState::ENABLED;

	public function __construct(
		public SessionAccount|null $account = null
	) {
	}

	public static function account(string $level, string $userId = self::SESSION_ACCOUNT_USER_ID, string $loginId = self::SESSION_ACCOUNT_LOGIN_ID, string $name = self::SESSION_ACCOUNT_NAME, $state = self::SESSION_ACCOUNT_STATE)
	{
		return new self(new SessionAccount(
			$userId,
			$loginId,
			$name,
			$level,
			$state
		));
	}
}
