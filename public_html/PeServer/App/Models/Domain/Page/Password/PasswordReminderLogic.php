<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Password;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Utc;
use PeServer\Core\Web\UrlUtility;

class PasswordReminderLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppCryptography $cryptography, private AppMailer $mailer, private AppTemplate $appTemplate)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'reminder_login_id',
		], false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->throwHttpStatusIfNotPasswordReminder(HttpStatus::NotFound);

		$this->validation('reminder_login_id', function (string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setSession(SessionKey::ANONYMOUS, new SessionAnonymous(passwordReminder: true));
			return;
		}

		$loginId = $this->getRequest('reminder_login_id');

		$email = Text::EMPTY;
		$token = UserUtility::generatePasswordReminderToken();
		$this->result = [
			'token' => $token,
		];

		$database = $this->openDatabase();
		$result = $database->transaction(function (IDatabaseContext $context) use ($loginId, $token, &$email) {
			$usersEntityDao = new UsersEntityDao($context);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($context);

			$userId = $usersEntityDao->selectUserIdByLoginId($loginId);
			if ($userId === null) {
				return false;
			}

			$userInfo = $usersEntityDao->selectUserInfoData($userId);
			$email = $userInfo->fields['email'];

			$this->writeAuditLogTargetUser($userId, AuditLog::USER_PASSWORD_REMINDER_TOKEN, ['token' => $token], $context);

			$userAuthenticationsEntityDao->updatePasswordReminder($userId, $token);

			return true;
		});

		if ($result) {
			$rawEmail = $this->cryptography->decrypt($email);

			$baseUrl = Text::replaceMap(
				Code::toLiteralString($this->config->setting->config->address->publicUrl),
				[
					'DOMAIN' => $this->config->setting->config->address->domain
				]
			);
			$url = UrlUtility::joinPath($baseUrl, "password/reset/$token");

			$subject = I18n::message('subject/password_reminder_token');
			$values = [
				'login_id' => $loginId,
				'url' => $url,
			];
			$html = $this->appTemplate->createMailTemplate('password_reminder_token', $subject, $values);

			$this->mailer->toAddresses = [
				new EmailAddress($rawEmail),
			];
			$this->mailer->subject = $subject;
			$this->mailer->setMessage(new EmailMessage(null, $html));

			$this->mailer->send();
		} else {
			//TODO: 待機処理
		}
	}

	#endregion
}
