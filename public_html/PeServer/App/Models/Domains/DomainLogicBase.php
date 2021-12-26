<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\Database;
use \Prophecy\Util\StringUtil;
use \PeServer\Core\ArrayUtility;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Throws\NotImplementedException;
use \PeServer\Core\Throws\InvalidOperationException;
use \PeServer\App\Models\Database\Entities\UserAuditLogsEntityDao;
use PeServer\Core\I18n;

abstract class DomainLogicBase extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	/**
	 * 監査用ユーザー情報の取得。
	 *
	 * @return array{userId:string}|null
	 */
	protected abstract function getAuditUserInfo(): array|null;

	private function writeAuditLogCore(string $userId, string $event, mixed $info, ?Database $database): void
	{
		$ipAddress = ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', '');
		$userAgent = ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', '');
		$dumpInfo = '';
		if (!is_null($info)) {
			$jsonText = json_encode($info, JSON_UNESCAPED_UNICODE);
			if ($jsonText !== false) {
				$dumpInfo = $jsonText;
			}
		}

		$db = $database ?? Database::open();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($db);
		$userAuditLogsEntityDao->insertLog($userId, $event, $dumpInfo, $ipAddress, $userAgent);
	}

	/**
	 * 現在ログインユーザーの監査ログ出力。
	 *
	 * ※DBじゃなくてテキストファイルでいいかも
	 *
	 * @param string $event
	 * @param mixed $info
	 * @return void
	 */
	protected function writeAuditLogCurrentUser(string $event, mixed $info = null, ?Database $database = null): void
	{
		$userInfo = $this->getAuditUserInfo();
		if (!ArrayUtility::tryGet($userInfo, 'userId', $userId)) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return;
		}

		$userId = $userInfo['userId']; // @phpstan-ignore-line ArrayUtility::tryGet

		$this->writeAuditLogCore($userId, $event, $info, $database);
	}

	protected function writeAuditLogTargetUser(string $userId, string $event, mixed $info = null, ?Database $database = null): void
	{
		if (StringUtility::isNullOrWhiteSpace($userId)) {
			$this->logger->error('監査ログ ユーザーID不正のため書き込み中止');
			return;
		}

		$this->writeAuditLogCore($userId, $event, $info, $database);
	}
}
