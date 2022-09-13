<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\ApplicationApi;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
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

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);

		$this->requestJson = $this->getRequestJson();
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validateJsonProperty($this->requestJson, 'user_id', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'version', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'revision', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'exception', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'mail_address', self::VALIDATE_JSON_PROPERTY_TEXT);
		$this->validateJsonProperty($this->requestJson, 'comment', self::VALIDATE_JSON_PROPERTY_TEXT);
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$values = [
			'user_id' => $this->requestJson['user_id'],
			'version' => $this->requestJson['version'],
			'revision' => $this->requestJson['revision'],
			'exception' => $this->requestJson['exception'],
			'email' => $this->requestJson['mail_address'] ?? Text::EMPTY,
			'comment' => $this->requestJson['comment'] ?? Text::EMPTY,
			'report' => (new JsonSerializer())->save($this->requestJson)->getRaw(),
		];

		throw new NotImplementedException();
	}

	#endregion
}
