<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Models\Data\EmailInformation;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Cryptography;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Web\UrlUtility;

class AccountSignupStep1Logic extends PageLogicBase
{
	use SessionAnonymousTrait;

	private const TEMP_TOKEN = 'sign_up_token';

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppCryptography $cryptography, private Mailer $mailer, private AppTemplate $appTemplate, private AppEmailInformation $appEmailInformation)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->setValue('email', $this->appEmailInformation);

		$this->registerParameterKeys([
			'value',
			'account_signup_token',
			'account_signup_value',
			'account_signup_email',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->throwHttpStatusIfNotSignup1(HttpStatus::NotFound);

		$this->validation('account_signup_email', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isEmail($key, $value);
		});

		$temp = $this->popTemporary(self::TEMP_TOKEN);
		$tempValue = Arr::getOr($temp, 'value', Text::EMPTY);
		$tempToken = Arr::getOr($temp, 'token', Text::EMPTY);

		$inputValue = $this->getRequest('account_signup_value');
		$inputToken = $this->getRequest('account_signup_token');

		if (!($tempValue === $inputValue && $tempToken == $inputToken)) {
			$this->addError('account_signup_value', I18n::message('error/sign_up_token'));
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setSession(SessionKey::ANONYMOUS, new SessionAnonymous(signup1: true));
			return;
		}

		$email = $this->getRequest('account_signup_email');
		$token = UserUtility::generateSignupToken();

		$params = [
			'token' => $token,
			'raw_email' => $email,
			'email' => $this->cryptography->encrypt($email),
			'mark_email' => $this->cryptography->toMark($email),
		];

		$this->logger->info('ユーザー登録処理開始: {0}', $params['token']);

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($context);

			$likeItems = $signUpWaitEmailsEntityDao->selectLikeEmails($params['mark_email']);
			foreach ($likeItems->rows as $likeItem) {
				$rawLikeEmail = $this->cryptography->decrypt($likeItem['email']);
				if ($rawLikeEmail === $params['raw_email']) {
					$this->logger->info('重複メールアドレスを破棄: {0}', $likeItem['token']);
					$signUpWaitEmailsEntityDao->deleteToken($likeItem['token']);
				}
			}

			$signUpWaitEmailsEntityDao->insertEmail(
				$params['token'],
				$params['email'],
				$params['mark_email'],
				$this->stores->special->getServer('REMOTE_ADDR'),
				$this->stores->special->getServer('HTTP_USER_AGENT')
			);

			return true;
		});

		//TODO: 設定からとるのかリダイレクトみたいにサーバーからとるのか混在中
		$baseUrl = Text::replaceMap(
			Code::toLiteralString($this->config->setting->config->address->publicUrl),
			[
				'DOMAIN' => $this->config->setting->config->address->domain
			]
		);
		$url = UrlUtility::joinPath($baseUrl, "account/signup/$token");

		$subject = I18n::message('subject/sign_up_step1');
		$values = [
			'url' => $url,
		];
		$html = $this->appTemplate->createMailTemplate('mail_signup_step1', $subject, $values);

		$this->mailer->toAddresses = [
			new EmailAddress($email),
		];
		$this->mailer->subject = $subject;
		$this->mailer->setMessage(new EmailMessage(null, $html));

		$this->mailer->send();
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Submit && Arr::containsKey($this->result, 'token')) {
			$this->removeTemporary(self::TEMP_TOKEN);
			return;
		}

		$tempToken = Cryptography::generateRandomBinary(10)->toHex();
		$tempValue = sprintf('%04d', Cryptography::generateRandomInteger(9999));
		$this->pushTemporary(self::TEMP_TOKEN, [
			'token' => $tempToken,
			'value' => $tempValue,
		]);
		$this->setValue('account_signup_token', $tempToken);
		$this->setValue('account_signup_value', Text::EMPTY);
		$this->setValue('value', $tempValue);
	}

	#endregion
}
