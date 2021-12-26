<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use \PeServer\App\Models\AuditLog;
use \PeServer\Core\I18n;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\AccountValidator;
use \PeServer\App\Models\Domains\Page\PageLogicBase;
use \PeServer\Core\Database;
use PeServer\Core\Uuid;

class SettingSetupLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeysImpl(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'setting_setup_login_id',
			'setting_setup_password',
			'setting_setup_user_name',
			'setting_setup_email',
			'setting_setup_website',
		], true);

		$this->setValue('setting_setup_password', '');
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('setting_setup_login_id', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isLoginId($key, $value);
		});

		$this->validation('setting_setup_password', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		});

		$this->validation('setting_setup_user_name', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isUserName($key, $value);
		});

		$this->validation('setting_setup_email', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$this->validation('setting_setup_website', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isWebsite($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			$this->executeInitialize($callMode);
		} else {
			$this->executeSubmit($callMode);
		}
	}

	private function executeInitialize(LogicCallMode $callMode): void
	{
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
		$params = [
			'login_id' => StringUtility::trim((string)$this->getRequest('setting_setup_login_id')),
			'password' => (string)$this->getRequest('setting_setup_password'),
			'user_name' => StringUtility::trim((string)$this->getRequest('setting_setup_user_name')),
			'email' => StringUtility::trim((string)$this->getRequest('setting_setup_email')),
			'website' => StringUtility::trim((string)$this->getRequest('setting_setup_website')),
		];

		$userInfo = [
			'id' => Uuid::generateGuid(),
			'generate_password' => '',
			'current_password' => password_hash($params['password'], PASSWORD_DEFAULT),
		];

		$database = Database::open();

		$result = $database->transaction(function ($db, $params, $userInfo) {
			$accountValidator = new AccountValidator($this, $this->validator);

			if (!$accountValidator->isFreeLoginId($db, 'setting_setup_login_id', $params['login_id'])) {
				return false;
			}

			// 管理者ユーザーの登録

			// 現在のセットアップユーザーを無効化

			// ユーザー生成記録を監査ログに追加
			$this->writeAuditLogCurrentUser(AuditLog::USER_CREATE, $userInfo['id'], $db);
			$this->writeAuditLogTargetUser($userInfo['id'], AuditLog::USER_GENERATED, null, $db);
		}, $params, $userInfo);

		// 生成したのであれば現在のセットアップユーザーは用済みなのでログアウト
		if ($result) {
			$this->shutdownSession();
		}
	}
}
