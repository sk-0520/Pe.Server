<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\Mime;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AppDatabase;
use PeServer\Core\Database\Database;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;


abstract class DomainLogicBase extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	/**
	 * データベース接続処理。
	 *
	 * @return Database
	 */
	protected function openDatabase(): Database
	{
		return AppDatabase::open($this->logger);
	}

	protected function setResponseJson(ResponseJson $responseJson): void
	{
		$result = [
			'data' => $responseJson->data,
		];
		if (!is_null($responseJson->error)) {
			$result['error'] = $responseJson->error;
		}

		$this->setJsonContent($result);
	}

	/**
	 * 監査ログ用ユーザー情報の取得。
	 *
	 * ドメインロジックで明示的に使用しない想定。
	 *
	 * @return array{user_id:string}|null
	 */
	protected abstract function getAuditUserInfo(): array|null;

	/**
	 * 監査ログ出力内部処理。
	 *
	 * @param string $userId
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param IDatabaseContext|null $context
	 * @return integer
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	private function writeAuditLogCore(string $userId, string $event, ?array $info, ?IDatabaseContext $context): int
	{
		$ipAddress = $this->special->getServer('REMOTE_ADDR');
		$userAgent = $this->special->getServer('HTTP_USER_AGENT');
		$dumpInfo = InitialValue::EMPTY_STRING;
		if (!is_null($info)) {
			$jsonText = json_encode($info, JSON_UNESCAPED_UNICODE);
			if ($jsonText !== false) {
				$dumpInfo = $jsonText;
			}
		}

		$db = $context ?? $this->openDatabase();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($db);
		$userAuditLogsEntityDao->insertLog($userId, $event, $dumpInfo, $ipAddress, $userAgent);
		return $userAuditLogsEntityDao->selectLastLogId();
	}

	/**
	 * 現在ログインユーザーの監査ログ出力。
	 *
	 * ※DBじゃなくてテキストファイルでいいかも
	 *
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param IDatabaseContext|null $context
	 * @return int
	 */
	protected function writeAuditLogCurrentUser(string $event, ?array $info = null, ?IDatabaseContext $context = null): int
	{
		$userInfo = $this->getAuditUserInfo();
		if (!ArrayUtility::tryGet($userInfo, 'user_id', $userId)) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return -1;
		}

		$userId = $userInfo['user_id']; // @phpstan-ignore-line ArrayUtility::tryGet

		return $this->writeAuditLogCore($userId, $event, $info, $context);
	}

	/**
	 * 対象ユーザーとして監査ログ出力。
	 *
	 * @param string $userId
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param IDatabaseContext|null $context
	 * @return integer
	 */
	protected function writeAuditLogTargetUser(string $userId, string $event, ?array $info = null, ?IDatabaseContext $context = null): int
	{
		if (StringUtility::isNullOrWhiteSpace($userId)) {
			$this->logger->error('監査ログ ユーザーID不正のため書き込み中止');
			return -1;
		}

		return $this->writeAuditLogCore($userId, $event, $info, $context);
	}
}
