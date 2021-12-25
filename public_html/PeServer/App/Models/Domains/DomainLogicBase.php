<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\App\Models\Database\Entities\UserAuditLogsEntityDao;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\ArrayUtility;
use PeServer\Core\Database;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\InvalidOperationException;
use Prophecy\Util\StringUtil;

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

	/**
	 * 監査ログ出力。
	 *
	 * ※DBじゃなくてテキストファイルでいいかも
	 *
	 * @param string $event
	 * @param mixed $info
	 * @return void
	 */
	protected function writeAuditLog(string $event, mixed $info = null, ?Database $database = null): void
	{
		$userInfo = $this->getAuditUserInfo();
		if (!ArrayUtility::tryGet($userInfo, 'userId', $userId)) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return;
		}

		$userId = $userInfo['userId'];
		$ipAddress = ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', '');
		$userAgent = ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', '');
		$dumpInfo = StringUtility::dump($info);

		$db = $database ?? Database::open();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($db);
		$userAuditLogsEntityDao->insertLog($userId, $event, $dumpInfo, $ipAddress, $userAgent);
	}
}
