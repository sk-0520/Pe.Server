<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Domain\DomainLogicBase;
use PeServer\App\Models\HttpHeaderName;
use PeServer\App\Models\IAuditUserInfo;
use PeServer\Core\ArrayUtility;
use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\NotImplementedException;

abstract class ApiLogicBase extends DomainLogicBase
{
	#[Inject] //@phpstan-ignore-next-line Inject
	private HttpRequest $request;

	private IAuditUserInfo|null $auditUserInfo = null;

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function getAuditUserInfo(): ?IAuditUserInfo
	{
		if ($this->auditUserInfo === null) {
			if ($this->request->httpHeader->existsHeader(HttpHeaderName::API_KEY)) {
				if ($this->request->httpHeader->existsHeader(HttpHeaderName::SECRET_KEY)) {
					$apiKeys = $this->request->httpHeader->getValues(HttpHeaderName::API_KEY);
					$secrets = $this->request->httpHeader->getValues(HttpHeaderName::SECRET_KEY);

					if (ArrayUtility::getCount($apiKeys) === 1 && ArrayUtility::getCount($secrets) === 1) {
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
}
