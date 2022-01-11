<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;


final class SignupStep2FilterMiddleware implements IMiddleware
{
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$tokenState = $argument->request->exists('token');
		if ($tokenState['exists'] && $tokenState['type'] == HttpRequest::REQUEST_URL) {
			$token = $argument->request->getValue('token');

			$database = AppDatabase::open();
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($database);

			if ($signUpWaitEmailsEntityDao->selectExistsToken($token, AppConfiguration::$config['config']['confirm']['sign_up_wait_email_minutes'])) {
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
