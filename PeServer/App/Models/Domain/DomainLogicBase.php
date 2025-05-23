<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\Inject;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Logic\LogicBase;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Serialization\ISerializer;
use PeServer\Core\Serialization\Json;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Serialization\SerializerBase;
use PeServer\Core\Text;

abstract class DomainLogicBase extends LogicBase
{
	#[Inject]
	protected IDatabaseConnection $databaseConnection;

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	/**
	 * データベース接続処理。
	 *
	 * @return DatabaseContext
	 */
	protected function openDatabase(): DatabaseContext
	{
		//return AppDatabase::open($this->logger);
		return $this->databaseConnection->open();
	}

	protected function setResponseJson(ResponseJson $responseJson): void
	{
		$result = [
			'data' => $responseJson->data,
		];
		if ($responseJson->error !== null) {
			$result['error'] = $responseJson->error;
		}

		$this->setJsonContent($result);
	}

	/**
	 * 監査ログ用ユーザー情報の取得。
	 *
	 * ドメインロジックで明示的に使用しない想定。
	 *
	 * @return IAuditUserInfo|null
	 */
	abstract protected function getAuditUserInfo(): ?IAuditUserInfo;

	/**
	 * 監査ログ出力内部処理。
	 *
	 * @param string $userId
	 * @param string $event
	 * @param mixed|null $info
	 * @param IDatabaseContext|null $context
	 * @return int
	 */
	private function writeAuditLogCore(string $userId, string $event, mixed $info, ?IDatabaseContext $context, ?ISerializer $serializer = null): int
	{
		$ipAddress = $this->stores->special->getServer('REMOTE_ADDR');
		$userAgent = $this->stores->special->getServer('HTTP_USER_AGENT');
		$dumpInfo = Text::EMPTY;
		if ($info !== null) {
			$serializer ??= new JsonSerializer();

			$dumpInfo = strval($serializer->save($info));
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
	 * @param mixed $info
	 * @param IDatabaseContext|null $context
	 * @return int
	 */
	protected function writeAuditLogCurrentUser(string $event, mixed $info = null, ?IDatabaseContext $context = null): int
	{
		$userInfo = $this->getAuditUserInfo();
		if ($userInfo === null) {
			$this->logger->error('監査ログ ユーザー情報取得失敗のため書き込み中止');
			return -1;
		}

		$userId = $userInfo->getUserId();

		return $this->writeAuditLogCore($userId, $event, $info, $context);
	}

	/**
	 * 対象ユーザーとして監査ログ出力。
	 *
	 * @param string $userId
	 * @param string $event
	 * @param array<mixed>|null $info
	 * @param IDatabaseContext|null $context
	 * @return int
	 */
	protected function writeAuditLogTargetUser(string $userId, string $event, ?array $info = null, ?IDatabaseContext $context = null): int
	{
		if (Text::isNullOrWhiteSpace($userId)) {
			$this->logger->error('監査ログ ユーザーID不正のため書き込み中止');
			return -1;
		}

		return $this->writeAuditLogCore($userId, $event, $info, $context);
	}
}
