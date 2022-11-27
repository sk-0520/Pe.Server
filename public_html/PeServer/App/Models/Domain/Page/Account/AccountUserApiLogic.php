<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use Error;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\ApiKeysEntityDao;
use PeServer\App\Models\Domain\ApiUtility;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class AccountUserApiLogic extends PageLogicBase
{
	private const API_KEY_RETRY_COUNT = 10;
	private bool $isRegister = false;

	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		/** @var SessionAccount */
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$database = $this->openDatabase();
		$apiKeysEntityDao = new ApiKeysEntityDao($database);
		$exist = $apiKeysEntityDao->selectExistsApiKeyByUserId($userInfo->userId);
		$this->isRegister = !$exist;
		$this->setValue('from_account_api_is_register', $this->isRegister);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		/** @var SessionAccount */
		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		if ($callMode === LogicCallMode::Initialize) {
			if (!$this->isRegister) {
				$database = $this->openDatabase();
				$apiKeysEntityDao = new ApiKeysEntityDao($database);
				$apiKeyInfo = $apiKeysEntityDao->selectApiKeyByUserId($userInfo->userId);

				$this->setValue('api_key', $apiKeyInfo->fields['api_key']);
				$this->setValue('created_timestamp', $apiKeyInfo->fields['created_timestamp']);
			}

			$secret = $this->popTemporary('secret_key');
			$this->setValue('secret_key', $secret);

			return;
		}

		$database = $this->openDatabase();
		if ($this->isRegister) {
			$secret = ApiUtility::generateSecret();

			$database->transaction(function ($context) use ($userInfo, $secret) {
				$apiKeysEntityDao = new ApiKeysEntityDao($context);

				$apiKey = '';
				$existsApiKey = true;
				for ($i = 0; $existsApiKey && $i < self::API_KEY_RETRY_COUNT; $i++) {
					$apiKey = ApiUtility::generateKey();
					$existsApiKey = $apiKeysEntityDao->selectExistsApiKeyByApiKey($apiKey);
				}
				if ($existsApiKey) {
					throw new Error((string)self::API_KEY_RETRY_COUNT);
				}

				$apiKeysEntityDao->insertApiKey($userInfo->userId, $apiKey, $secret);

				$this->writeAuditLogCurrentUser(AuditLog::USER_API_KEY_REGISTER, ['api_key' => $apiKey, 'secret' => $secret], $context);

				return true;
			});

			$this->addTemporaryMessage('APIキーを登録しました');
			$this->addTemporaryMessage('シークレットキーは大事に保存してください');

			$this->pushTemporary('secret_key', $secret);
		} else {
			$database->transaction(function ($context) use ($userInfo) {
				$apiKeysEntityDao = new ApiKeysEntityDao($context);
				$apiKeysEntityDao->deleteApiKeyByUserId($userInfo->userId);

				$this->writeAuditLogCurrentUser(AuditLog::USER_API_KEY_UNREGISTER, null, $context);

				return true;
			});

			$this->addTemporaryMessage('APIキーを削除しました');
			$this->removeTemporary('secret_key');
		}

		$this->dbCache->exportUserInformation();
	}

	#endregion
}
