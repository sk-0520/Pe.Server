<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Regex;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Throws\NotImplementedException;

/**
 * HTTPS 遷移ミドルウェア。
 */
class HttpsMiddleware implements IMiddleware
{
	public function __construct(
		private ILogger $logger,
	) {
	}

	#region IMiddleware

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if (!$argument->stores->special->isHttps()) {
			$originUrl = $argument->stores->special->getRequestUrl();
			$httpsUrl = $originUrl->changeScheme('https');
			$this->logger->info('[REDIRECT] HTTP -> HTTPS');
			return MiddlewareResult::redirect($httpsUrl);
		}

		return MiddlewareResult::none();
	}

	final public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	#endregion
}
