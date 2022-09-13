<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\App\Models\HttpHeaderName;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\Core\Collections\Arr;
use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Utc;

abstract class ApiLogicBase extends DomainLogicBase
{
	#region define

	protected const VALIDATE_JSON_PROPERTY_TEXT = 1;
	protected const VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT = 2;
	protected const VALIDATE_JSON_PROPERTY_INT = 3;
	protected const VALIDATE_JSON_PROPERTY_TIMESTAMP_UTC = 4;

	#endregion

	#region variable

	#[Inject] //@phpstan-ignore-next-line Inject
	private HttpRequest $request;

	private IAuditUserInfo|null $auditUserInfo = null;

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region function

	/**
	 * JSONプロパティ型判定処理。
	 *
	 * @param array<string,mixed> $json
	 * @param string $key
	 * @param int $validateJsonProperty
	 * @param string|null $parent
	 * @return bool
	 */
	protected function validateJsonProperty(array $json, string $key, int $validateJsonProperty, ?string $parent = null): bool
	{
		if (isset($json[$key])) {
			switch ($validateJsonProperty) {
				case self::VALIDATE_JSON_PROPERTY_TEXT:
				case self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT:
					if (is_string($json[$key])) {
						if ($validateJsonProperty === self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT) {
							return !Text::isNullOrWhiteSpace($json[$key]);
						}
						return true;
					}
					if ($validateJsonProperty === self::VALIDATE_JSON_PROPERTY_TEXT && $json[$key] === null) { //@phpstan-ignore-line
						return true;
					}
					break;

				case self::VALIDATE_JSON_PROPERTY_INT:
					if (is_int($json[$key])) {
						return true;
					}
					break;

				case self::VALIDATE_JSON_PROPERTY_TIMESTAMP_UTC:
					if (is_string($json[$key])) {
						return Utc::tryParse($json[$key], $unused);
					}

				default:
					break;
			}
		} else {
			$errorKey = Text::isNullOrWhiteSpace($parent)
				? $key
				: $parent . '.' . $key;
			$this->addError($errorKey, 'not found property');
		}

		return false;
	}

	#endregion

	#region DomainLogicBase

	protected function getAuditUserInfo(): ?IAuditUserInfo
	{
		if ($this->auditUserInfo === null) {
			if ($this->request->httpHeader->existsHeader(HttpHeaderName::API_KEY)) {
				if ($this->request->httpHeader->existsHeader(HttpHeaderName::SECRET_KEY)) {
					$apiKeys = $this->request->httpHeader->getValues(HttpHeaderName::API_KEY);
					$secrets = $this->request->httpHeader->getValues(HttpHeaderName::SECRET_KEY);

					if (Arr::getCount($apiKeys) === 1 && Arr::getCount($secrets) === 1) {
						$apiKey = $apiKeys[0];
						$secret = $secrets[0];

						$database = $this->openDatabase();
						$userDomainDao = new UserDomainDao($database);
						$userId = $userDomainDao->selectUserIdFromApiKey($apiKey, $secret);

						if ($userId !== null) {
							$this->auditUserInfo = new class($userId) implements IAuditUserInfo
							{
								public function __construct(private string $userId)
								{
								}

								//[IAuditUserInfo]

								public function getUserId(): string
								{
									return $this->userId;
								}
							};
						}
					}
				}
			}
		}

		return $this->auditUserInfo;
	}

	#endregion
}
