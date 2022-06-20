<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Http\HttpRequestExists;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;


final class SignupStep2FilterMiddleware implements IMiddleware
{
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$tokenState = $argument->request->exists('token');
		if ($tokenState->exists && $tokenState->kind === HttpRequestExists::KIND_URL) {
			$token = $argument->request->getValue($tokenState->name);

			$database = AppDatabase::open();
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($database);

			/** @var int @-phpstan-ignore-next-line */
			$limitMinutes = AppConfiguration::$config['config']['confirm']['sign_up_wait_email_minutes'];
			if ($signUpWaitEmailsEntityDao->selectExistsToken($token, $limitMinutes)) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
