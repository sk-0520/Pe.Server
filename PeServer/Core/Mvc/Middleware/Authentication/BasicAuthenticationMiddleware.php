<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware\Authentication;

use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;

class BasicAuthenticationMiddleware extends AuthenticationMiddlewareBase
{
	public function __construct(ILogger $logger)
	{
		parent::__construct($logger);
	}

	#region function

	/**
	 * 資格情報取得。
	 *
	 * 参考実装としての処理であって継承側でいい感じにする想定。
	 *
	 * @return Dictionary<string>
	 */
	protected function getCredentials(): Dictionary
	{
		return new Dictionary(TypeUtility::TYPE_STRING, [
			"user" => "password",
		]);
	}

	#endregion

	#region AuthenticationMiddlewareBase

	protected function authenticate(MiddlewareArgument $argument): MiddlewareResult
	{
		$authUser = $argument->stores->special->getServer("PHP_AUTH_USER");

		// @phpstan-ignore staticMethod.alreadyNarrowedType
		if (Text::isNullOrWhiteSpace($authUser)) {
			return MiddlewareResult::error(HttpStatus::Unauthorized);
		}

		return MiddlewareResult::none();
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		if ($response->status === HttpStatus::Unauthorized) {
			$response->header->setValue("WWW-Authenticate", 'Basic realm="Enter username and password."');
		}

		return MiddlewareResult::none();
	}


	#endregion

}
