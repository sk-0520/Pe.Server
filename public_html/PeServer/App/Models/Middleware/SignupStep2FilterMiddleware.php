<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabase;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpRequestExists;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;


final class SignupStep2FilterMiddleware implements IMiddleware
{
	public function __construct(
		private AppConfiguration $config,
		private IDatabaseConnection $connection
	) {
	}

	//[IMiddleware]

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$tokenState = $argument->request->exists('token');
		if ($tokenState->exists && $tokenState->kind === HttpRequestExists::KIND_URL) {
			$token = $argument->request->getValue($tokenState->name);

			$database = $this->connection->open();
			$signUpWaitEmailsEntityDao = new SignUpWaitEmailsEntityDao($database);

			$limitMinutes = $this->config->setting->config->confirm->signUpWaitEmailMinutes;
			if ($signUpWaitEmailsEntityDao->selectExistsToken($token, $limitMinutes)) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
