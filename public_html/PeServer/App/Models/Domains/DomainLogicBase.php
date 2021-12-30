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
		return Database::open();
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

	private function writeAuditLogCore(string $userId, string $event, mixed $info, ?Database $database): void
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
		$userInfo = $this->getUserInfo();
		if (!ArrayUtility::tryGet($userInfo, 'user_id', $userId)) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return;
		}

		$userId = $userInfo['user_id']; // @phpstan-ignore-line ArrayUtility::tryGet

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
