<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page;

use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\Domain\DomainLogicBase;

abstract class PageLogicBase extends DomainLogicBase
{
	public const TEMP_MESSAGES = 'temp_messages';

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): array|null
	{
		if (!SessionManager::existsAccount()) {
			return null;
		}

		$account = SessionManager::getAccount();

		return ['user_id' => $account['user_id']];
	}

	protected function addTemporaryMessage(string $message): void
	{
		/** @var string[] */
		$messages = $this->peekTemporary(self::TEMP_MESSAGES, []);
		$messages[] = $message;
		$this->pushTemporary(self::TEMP_MESSAGES, $messages);
	}
}
