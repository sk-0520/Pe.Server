<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page;

use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Mvc\LogicParameter;

abstract class PageLogicBase extends DomainLogicBase
{
	#region define

	public const TEMP_MESSAGES = 'temp_messages';

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region function

	protected function getAuditUserInfo(): ?IAuditUserInfo
	{
		if (!$this->existsSession(SessionKey::ACCOUNT)) {
			return null;
		}

		$account = $this->requireSession(SessionKey::ACCOUNT);

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

	#endregion
}
