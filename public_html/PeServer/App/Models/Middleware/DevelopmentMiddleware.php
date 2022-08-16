<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Environment;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;


final class DevelopmentMiddleware implements IMiddleware
{
	public function __construct(
		private ILogger $logger
	) {
	}

	//[IMiddleware]

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if (Environment::isProduction()) {
			$this->logger->warn('本番環境での実行は抑制');
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		return MiddlewareResult::none();
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
