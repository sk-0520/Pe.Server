<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\ApplicationApi;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Dao\Entities\SequenceEntityDao;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Archiver;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mail\Attachment;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class ApplicationApiCrashReportLogic extends ApiLogicBase
{
	#region variable

	/**
	 * 要求JSON
	 *
	 * @var array<string,mixed>
	 */
	private array $requestJson;

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppCryptography $appCryptography, private AppMailer $mailer, private AppTemplate $appTemplate)
	{
		parent::__construct($parameter);

		$this->requestJson = $this->getRequestJson();
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validateJsonProperty($this->requestJson, 'version', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'revision', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'build', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'user_id', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'exception', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'mail_address', self::VALIDATE_JSON_PROPERTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'comment', self::VALIDATE_JSON_PROPERTY_TEXT);

		// 完全に実装ミス
		if ($this->hasError()) {
			throw new Exception();
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$mailAddress = Text::isNullOrWhiteSpace($this->requestJson['mail_address'])
			? Text::EMPTY
			: $this->appCryptography->encrypt($this->requestJson['mail_address']);
		$requestJson = $this->requestJson;
		unset($requestJson['mail_address']);

		$binaryReport = (new JsonSerializer())->save($requestJson);
		$compressReport = Archiver::compressGzip($binaryReport);

		$sequence = 0;

		$database = $this->openDatabase();
		$result = $database->transaction(function (IDatabaseContext $context) use ($mailAddress, $requestJson, $compressReport, &$sequence) {
			$crashReportsEntityDao = new CrashReportsEntityDao($context);
			$crashReportsEntityDao->insertCrashReports(
				$this->stores->special->getServer('REMOTE_ADDR'),
				$requestJson['version'],
				$requestJson['revision'],
				$requestJson['build'],
				$requestJson['user_id'],
				$requestJson['exception'],
				$mailAddress,
				$requestJson['comment'] ?? Text::EMPTY,
				$compressReport
			);

			$sequenceEntityDao = new SequenceEntityDao($context);

			$sequence = $sequenceEntityDao->getLastSequence();

			$this->setContent(Mime::JSON, [
				'success' => true,
				'message' => '',
			]);

			return true;
		});

		if (!$result) {
			// 完全に実装ミス
			throw new Exception();
		}

		// メール送信
		$crashReportEmails = $this->config->setting->config->address->notify->crashReport;
		$exception = Text::splitLines($requestJson['exception'])[0];

		$this->mailer->customSubjectHeader = '[Pe-CrashReport]';
		$this->mailer->subject = "$sequence: $exception";
		$message = $this->appTemplate->createMailTemplate('crash_report_email', $this->mailer->subject, $requestJson);
		$this->mailer->setMessage(new EmailMessage($message));
		$this->mailer->attachments[] = new Attachment("report-$sequence.json", $this->getRequestContent(), Mime::JSON);
		foreach ($crashReportEmails as $crashReportEmail) {
			$this->mailer->toAddresses = [
				new EmailAddress($crashReportEmail),
			];

			try {
				$this->mailer->send();
			} catch (Exception $ex) {
				// メール送信は開発側の都合なのでエラーならログに記録するのみ
				$this->logger->error($ex);
			}
		}
	}

	#endregion
}
