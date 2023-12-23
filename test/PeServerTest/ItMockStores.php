<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\UserState;

final class ItMockStores
{
	public const SESSION_ACCOUNT_USER_ID = 'session-account-user-id';
	public const SESSION_ACCOUNT_LOGIN_ID = 'session-account-login-id';
	public const SESSION_ACCOUNT_PASSWORD = 'password';
	public const SESSION_ACCOUNT_NAME = 'session-account-name';
	public const SESSION_ACCOUNT_STATE = UserState::ENABLED;
	public const SESSION_ACCOUNT_EMAIL = self::SESSION_ACCOUNT_NAME . '@localhost.domain';
	public const SESSION_ACCOUNT_MARKER = 0;
	public const SESSION_ACCOUNT_WEBSITE = 'http://localhost';
	public const SESSION_ACCOUNT_DESCRIPTION = 'description';
	public const SESSION_ACCOUNT_NOTE = 'note';

	public function __construct(
		public SessionAccount|SessionAnonymous|null $account,
		public bool $enabledSetupUser
	) {
	}

	public static function none()
	{
		return new self(null, false);
	}

	public static function account(string $level, string $userId = self::SESSION_ACCOUNT_USER_ID, string $loginId = self::SESSION_ACCOUNT_LOGIN_ID, string $name = self::SESSION_ACCOUNT_NAME, $state = self::SESSION_ACCOUNT_STATE, bool $enabledSetupUser = false)
	{
		return new self(
			new SessionAccount(
				$userId,
				$loginId,
				$name,
				$level,
				$state
			),
			$enabledSetupUser
		);
	}

	public static function anonymous(bool $login = false, bool $signup1 = false, bool $signup2 = false, bool $passwordReminder = false, bool $passwordReset = false, bool $enabledSetupUser = false)
	{
		return new self(
			new SessionAnonymous(
				$login,
				$signup1,
				$signup2,
				$passwordReminder,
				$passwordReset,
			),
			$enabledSetupUser
		);
	}
}
