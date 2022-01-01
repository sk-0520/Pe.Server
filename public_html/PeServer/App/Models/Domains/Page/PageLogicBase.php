<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page;

use PeServer\App\Controllers\Page\PageControllerBase;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\DomainLogicBase;
use PeServer\App\Models\SessionManager;

abstract class PageLogicBase extends DomainLogicBase
{
	public const TEMP_MESSAGES = 'temp_messages';

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getUserInfo(): array|null
	{
		if (!SessionManager::hasAccount()) {
			return null;
		}

		$account = SessionManager::getAccount();

		return ['user_id' => $account['user_id']];
	}

	protected function addTemporaryMessage(string $message): void
	{
		$messages = $this->peekTemporary(self::TEMP_MESSAGES, []);
		$messages[] = $message;
		$this->pushTemporary(self::TEMP_MESSAGES, $messages);
	}
}
