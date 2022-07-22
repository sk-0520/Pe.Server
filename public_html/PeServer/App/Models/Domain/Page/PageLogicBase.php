<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page;

use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\LogicParameter;

abstract class PageLogicBase extends DomainLogicBase
{
	public const TEMP_MESSAGES = 'temp_messages';

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): ?IAuditUserInfo
	{
		if (!SessionManager::existsAccount()) {
			return null;
		}

		$account = SessionManager::getAccount();

		return new class($account) implements IAuditUserInfo
		{
			private string $userId;

			public function __construct(SessionAccount $account)
			{
				$this->userId = $account->userId;
			}

			public function getUserId(): string
			{
				return $this->userId;
			}
		};
	}

	protected function addTemporaryMessage(string $message): void
	{
		/** @var string[] */
		$messages = $this->peekTemporary(self::TEMP_MESSAGES, []);
		$messages[] = $message;
		$this->pushTemporary(self::TEMP_MESSAGES, $messages);
	}
}
