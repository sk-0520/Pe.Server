<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\ApplicationApi;

use \Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\NotImplementedException;

class ApplicationApiFeedbackLogic extends ApiLogicBase
{
	#region variable

	/**
	 * 要求JSON
	 *
	 * @var array<string,mixed>
	 */
	private array $requestJson;

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppMailer $mailer, private AppTemplate $appTemplate)
	{
		parent::__construct($parameter);

		$this->requestJson = $this->getRequestJson();
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validateJsonProperty($this->requestJson, 'kind', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'subject', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'content', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);

		$this->validateJsonProperty($this->requestJson, 'version', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'revision', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'build', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);

		$this->validateJsonProperty($this->requestJson, 'user_id', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'first_execute_timestamp', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'first_execute_version', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);

		$this->validateJsonProperty($this->requestJson, 'process', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'platform', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'os', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'clr', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);

		// 完全に実装ミス
		if ($this->hasError()) {
			throw new Exception();
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$sequence = 0;

		$database = $this->openDatabase();
		$result = $database->transaction(function (IDatabaseContext $context) use (&$sequence) {
			$feedbacksEntityDao = new FeedbacksEntityDao($context);
			$feedbacksEntityDao->insertFeedbacks(
				$this->stores->special->getServer('REMOTE_ADDR'),

				$this->requestJson['version'],
				$this->requestJson['revision'],
				$this->requestJson['build'],
				$this->requestJson['user_id'],

				$this->requestJson['first_execute_timestamp'],
				$this->requestJson['first_execute_version'],

				$this->requestJson['process'],
				$this->requestJson['platform'],
				$this->requestJson['os'],
				$this->requestJson['clr'],

				$this->requestJson['kind'],
				$this->requestJson['subject'],
				$this->requestJson['content']
			);

			return true;
		});

		if (!$result) {
			// 完全に実装ミス
			throw new Exception();
		}

		// メール送信
		throw new NotImplementedException();
		// $this->setResponseJson(ResponseJson::success([
		// 	'size' => $size,
		// ]));
	}

	#endregion
}
