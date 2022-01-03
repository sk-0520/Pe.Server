<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\Core\I18n;
use PeServer\Core\Database;
use \Prophecy\Util\StringUtil;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\Validations;
use PeServer\App\Models\AppDatabase;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\App\Models\Database\Entities\UserAuditLogsEntityDao;

abstract class DomainLogicBase extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	/**
	 * ユーザー情報の取得。
	 *
	 * @return array{user_id:string}|null
	 */
	protected abstract function getUserInfo(): array|null;

	/**
	 * データベース接続処理。
	 *
	 * @return Database
	 */
	protected function openDatabase(): Database
	{
		return AppDatabase::open($this->logger);
	}

	/**
	 * ユーザー情報の取得。
	 *
	 * @return array{user_id:string}
	 * @throws InvalidOperationException 取れないとき。
	 */
	protected final function userInfo(): array
	{
		$userInfo = $this->getUserInfo();

		if (is_null($userInfo)) {
			throw new InvalidOperationException();
		}

		return $userInfo;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param Database|null $database
	 * @return integer
	 */
	private function writeAuditLogCore(string $userId, string $event, ?array $info, ?Database $database): int
	{
		/** @var string */
		$ipAddress = ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', '');
		/** @var string */
		$userAgent = ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', '');
		$dumpInfo = '';
		if (!is_null($info)) {
			$jsonText = json_encode($info, JSON_UNESCAPED_UNICODE);
			if ($jsonText !== false) {
				$dumpInfo = $jsonText;
			}
		}

		$db = $database ?? $this->openDatabase();
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
	 * @return int
	 */
	protected function writeAuditLogCurrentUser(string $event, ?array $info = null, ?Database $database = null): int
	{
		$userInfo = $this->getUserInfo();
		if (!ArrayUtility::tryGet($userInfo, 'user_id', $userId)) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return -1;
		}

		$userId = $userInfo['user_id']; // @phpstan-ignore-line ArrayUtility::tryGet

		return $this->writeAuditLogCore($userId, $event, $info, $database);
	}

	/**
	 * 対象ユーザーとして監査ログ出力。
	 *
	 * @param string $userId
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param Database|null $database
	 * @return integer
	 */
	protected function writeAuditLogTargetUser(string $userId, string $event, ?array $info = null, ?Database $database = null): int
	{
		if (StringUtility::isNullOrWhiteSpace($userId)) {
			$this->logger->error('監査ログ ユーザーID不正のため書き込み中止');
			return -1;
		}

		return $this->writeAuditLogCore($userId, $event, $info, $database);
	}
}
