<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\Core\HttpStatus;
use PeServer\Core\Environment;
use PeServer\Core\IMiddleware;
use PeServer\Core\MiddlewareResult;
use PeServer\Core\MiddlewareArgument;


final class DevelopmentMiddleware implements IMiddleware
{
	public function handle(MiddlewareArgument $argument): MiddlewareResult
	{
		if (Environment::isProduction()) {
			$argument->logger->warn('本番環境での実行は抑制');
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		return MiddlewareResult::none();
	}
}
