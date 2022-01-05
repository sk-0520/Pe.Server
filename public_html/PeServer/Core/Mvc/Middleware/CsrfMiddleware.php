<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Csrf;
use PeServer\Core\HttpStatus;
use PeServer\Core\IMiddleware;
use PeServer\Core\MiddlewareResult;
use PeServer\Core\MiddlewareArgument;


class CsrfMiddleware implements IMiddleware
{
	public function handle(MiddlewareArgument $argument): MiddlewareResult
	{
		$result = $argument->request->exists(Csrf::REQUEST_KEY);
		if (!$result['exists']) {
			$argument->logger->warn('要求CSRFトークンなし');
			return MiddlewareResult::error(HttpStatus::forbidden(), 'CSRF');
		}

		$requestToken = $argument->request->getValue(Csrf::REQUEST_KEY);
		if ($argument->session->tryGet(Csrf::SESSION_KEY, $sessionToken)) {
			if ($requestToken === $sessionToken) {
				return MiddlewareResult::none();
			}
			$argument->logger->warn('CSRFトークン不一致');
		} else {
			$argument->logger->warn('セッションCSRFトークンなし');
		}

		return MiddlewareResult::error(HttpStatus::forbidden(), 'CSRF');
	}
}
