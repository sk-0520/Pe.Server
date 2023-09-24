<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Password;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Cryptography;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class PasswordResetLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(
		LogicParameter $parameter,
		private AppConfiguration $config,
	) {
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->throwHttpStatusIfNotPasswordReset(HttpStatus::NotFound);

		$this->validation('reminder_login_id', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});

		//TODO: AccountUserPasswordLogic と同じなんよ
		$this->validation('reminder_password_new', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		}, ['trim' => false]);

		$this->validation('reminder_password_confirm', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
			$newPassword = $this->getRequest('reminder_password_new', Text::EMPTY, false);
			if ($value !== $newPassword) {
				$this->addError($key, I18n::message('error/password_confirm'));
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setSession(SessionKey::ANONYMOUS, new SessionAnonymous(passwordReset: true));
			return;
		}

		$userId = Text::EMPTY;

		$database = $this->openDatabase();
		$result = $database->transaction(function (IDatabaseContext $context) use (&$userId) {
			$usersEntityDao = new UsersEntityDao($context);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);

			$token = $this->getRequest('token');
			$loginId = $this->getRequest('reminder_login_id');
			$rawPassword = $this->getRequest('reminder_password_new', Text::EMPTY, false);

			$userId = $usersEntityDao->selectUserIdByLoginId($loginId);
			if ($userId === null) {
				$this->logger->warn('not fount loginId: {}', $userId);
				return false;
			}

			if (!$userAuthenticationsEntityDao->selectExistsToken($token, $this->config->setting->config->confirm->passwordReminderEmailMinutes)) {
				$this->logger->warn('not fount token: {}, userId: {}', $token, $userId);
				return false;
			}

			$password = Cryptography::toHashPassword($rawPassword);
			$userAuthenticationsEntityDao->updateResetPassword($userId, $password);

			$this->writeAuditLogTargetUser($userId, AuditLog::USER_PASSWORD_REMINDER_RESET, ['token' => $token], $context);

			return true;
		});

		$this->addTemporaryMessage('パスワード変更が実施されました');
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		$this->setValue('token', $this->getRequest('token'));
		$this->setValue('reminder_login_id', $this->getRequest('reminder_login_id'));
	}

	#endregion
}
